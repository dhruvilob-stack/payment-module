<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFees;
use Illuminate\Http\Request;

class RazorpayPaymentController extends Controller
{
    public function initialPayment($id, $logId)
    {
        $student = Student::findOrFail($id);
        $feeLog = StudentFees::where('id', $logId)->where('student_id', $student->id)->firstOrFail();

        $key = (string) env('RAZORPAY_TEST_KEY', '');
        $secret = (string) env('RAZORPAY_TEST_SECRET', '');

        if ($key === '' || $secret === '') {
            return redirect()->route('student.index')->with('error', 'Razorpay credentials are missing in .env.');
        }

        $amountInPaise = (int) round(((float) $feeLog->amount) * 100);
        $orderPayload = [
            'receipt' => 'fee_' . $feeLog->id . '_' . time(),
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'payment_capture' => 1,
        ];
        $order = $this->createOrder($orderPayload, $key, $secret);

        if (!$order || !isset($order['id'])) {
            return redirect()->route('student.index')->with('error', 'Unable to create Razorpay order.');
        }

        $feeLog->update([
            'gateway' => 'razorpay',
            'ref_id' => $order['id'],
            'status' => 'pending',
            'logs' => json_encode([
                'stage' => 'order_created',
                'order' => $order,
            ]),
        ]);

        return view('payment.razorpay-checkout', [
            'student' => $student,
            'feeLog' => $feeLog,
            'order' => $order,
            'razorpayKey' => $key,
            'amountInPaise' => $amountInPaise,
        ]);
    }

    public function callback(Request $request, $id, $logId)
    {
        $student = Student::findOrFail($id);
        $feeLog = StudentFees::where('id', $logId)->where('student_id', $student->id)->firstOrFail();

        $payload = $request->all();
        $secret = (string) env('RAZORPAY_TEST_SECRET', '');

        if (($payload['razorpay_status'] ?? '') === 'failed') {
            $feeLog->update([
                'gateway' => 'razorpay',
                'status' => 'failed',
                'logs' => json_encode($payload),
            ]);

            return redirect()->route('student.index')->with('error', 'Payment failed or was cancelled.');
        }

        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $expectedSignature = hash_hmac(
            'sha256',
            $request->input('razorpay_order_id') . '|' . $request->input('razorpay_payment_id'),
            $secret
        );

        $isValid = hash_equals($expectedSignature, $request->input('razorpay_signature'));

        if (!$isValid) {
            $feeLog->update([
                'gateway' => 'razorpay',
                'status' => 'failed',
                'logs' => json_encode([
                    'payload' => $payload,
                    'error' => 'Signature verification failed',
                ]),
            ]);

            return redirect()->route('student.index')->with('error', 'Payment verification failed.');
        }

        $feeLog->update([
            'gateway' => 'razorpay',
            'ref_id' => $request->input('razorpay_payment_id'),
            'status' => 'success',
            'logs' => json_encode($payload),
        ]);

        return redirect()->route('student.index')->with('payment_success', 'Payment successful!');
    }

    private function createOrder(array $payload, string $key, string $secret): ?array
    {
        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, $key . ':' . $secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode < 200 || $httpCode >= 300) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }
}
