<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFees;
use Illuminate\Http\Request;

class PhonePePaymentController extends Controller
{
    public function initialPayment($id, $logId){
        $item = Student::findOrFail($id);

        $data = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'merchantTransactionId' => uniqid('txn_'),
            'merchantUserId' => 'MUID123',
            'amount' => (100 * 100),
            'redirectUrl' => route('phonepe.callback', ['id' => $item->id, 'logId' => $logId]),
            'redirectMode' => 'POST', // Fixed typo: redirecMode -> redirectMode
            'callbackUrl' => route('phonepe.callback', ['id' => $item->id, 'logId' => $logId]),
            'mobileNumber' => $item->phone,
            'paymentInstrument' => [
                "type" => "PAY_PAGE",
            ],
        ];

        $encode = base64_encode(json_encode($data));
        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = env('PHONEPE_SALT_INDEX', 1); // Ensure this is 1

        // FIX 1: Use Salt Key here
        $string = $encode . "/pg/v1/pay" . $saltKey; 
        $sha256 = hash('sha256', $string);

        // FIX 2: Use Salt Index here
        $finalXheader = $sha256 . "###" . $saltIndex;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json",
            "X-VERIFY: " . $finalXheader // Correct header format
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        // FIX 3: Send the encoded string wrapped in a 'request' key
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $encode]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        // dd($response);
        if (isset($response['success']) && $response['success'] === true) {
            return redirect($response['data']['instrumentResponse']['redirectInfo']['url']);
        } else {
            return redirect()->back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }
    public function callback(Request $request, $id, $logId){
        $data = $request->all();
        $requestCode = strtoupper((string) ($data['code'] ?? ''));
        $requestStatus = strtoupper((string) ($data['status'] ?? ''));
        $merchantTransactionId = (string) ($data['transactionId'] ?? '');
        $providerReferenceId = $data['providerReferenceId'] ?? null;

        $normalizedStatus = $this->normalizeStatus($requestCode, $requestStatus);

        // Redirect response can be PAYMENT_PENDING, so verify final state using status API.
        if ($merchantTransactionId !== '' && $normalizedStatus === 'pending') {
            $statusResponse = $this->fetchPhonePeStatus($merchantTransactionId);
            $normalizedStatus = $statusResponse['status'];
            $providerReferenceId = $statusResponse['provider_reference_id'] ?? $providerReferenceId;
            $data['status_api'] = $statusResponse['raw'];
        }

        StudentFees::where('id', $logId)->update([
            'ref_id' => $providerReferenceId ?? ($data['transactionId'] ?? null),
            'gateway' => 'phonepe',
            'logs' => json_encode($data),
            'status' => $normalizedStatus,
        ]);

        if ($normalizedStatus === 'success') {
            return redirect()->route('student.index')->with('payment_success', 'Payment successful!');
        }

        if ($normalizedStatus === 'pending') {
            return redirect()->route('student.index')->with('warning', 'Payment is pending. Please refresh in a moment.');
        }

        return redirect()->route('student.index')->with('error', 'Payment failed or was cancelled.');
    }   

    private function normalizeStatus(string $code, string $status): string
    {
        $code = strtoupper($code);
        $status = strtoupper($status);

        if (in_array($code, ['PAYMENT_SUCCESS', 'SUCCESS'], true) || in_array($status, ['SUCCESS', 'COMPLETED'], true)) {
            return 'success';
        }

        if (in_array($code, ['PAYMENT_ERROR', 'PAYMENT_FAILED', 'PAYMENT_DECLINED'], true) || in_array($status, ['FAILED', 'DECLINED', 'ERROR'], true)) {
            return 'failed';
        }

        return 'pending';
    }

    private function fetchPhonePeStatus(string $merchantTransactionId): array
    {
        $merchantId = env('PHONEPE_MERCHANT_ID');
        $saltKey = env('PHONEPE_SALT_KEY');
        $saltIndex = env('PHONEPE_SALT_INDEX', 1);

        $path = "/pg/v1/status/{$merchantId}/{$merchantTransactionId}";
        $xVerify = hash('sha256', $path . $saltKey) . '###' . $saltIndex;
        $url = "https://api-preprod.phonepe.com/apis/pg-sandbox{$path}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            "X-VERIFY: {$xVerify}",
            "X-MERCHANT-ID: {$merchantId}",
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || !$response) {
            return [
                'status' => 'pending',
                'provider_reference_id' => null,
                'raw' => ['error' => $curlError ?: 'Empty status response'],
            ];
        }

        $decoded = json_decode($response, true);
        $state = strtoupper((string) ($decoded['data']['state'] ?? ''));
        $providerReferenceId = $decoded['data']['transactionId'] ?? null;

        if ($state === 'COMPLETED') {
            return [
                'status' => 'success',
                'provider_reference_id' => $providerReferenceId,
                'raw' => $decoded,
            ];
        }

        if (in_array($state, ['FAILED', 'DECLINED', 'EXPIRED'], true)) {
            return [
                'status' => 'failed',
                'provider_reference_id' => $providerReferenceId,
                'raw' => $decoded,
            ];
        }

        return [
            'status' => 'pending',
            'provider_reference_id' => $providerReferenceId,
            'raw' => $decoded,
        ];
    }
}
