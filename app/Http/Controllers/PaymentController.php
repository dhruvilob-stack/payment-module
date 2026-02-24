<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentFees;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payFee($id)
    {
        // Logic to handle fee payment for the student with the given ID
        // This could involve fetching the student, calculating the fee, and processing the payment
        $data = Student::findOrFail($id);
        return view('payment.pay-fee',compact('data'));
    }   

    public function gateway(Request $request, $id)
    {
        $input = $request->all();
        $request->validate([
            'gateway' => 'required|in:phonepe,razorpay',
        ]);

        $fees['student_id'] = $id;
        $fees['amount'] = 5000; // Assuming a fixed fee amount for demonstration
        $fees['gateway'] = $input['gateway'];

        $log = StudentFees::create($fees);

        switch ($input['gateway']) {
            case 'phonepe':
                return redirect()->route('phonepe.gateway', [$id, $log->id])->with('success', 'Redirecting to PhonePe payment gateway...');
            case 'razorpay':
                return redirect()->route('razorpay.gateway', [$id, $log->id])->with('success', 'Redirecting to Razorpay payment gateway...');
            default:
                return redirect()->back()->with('error', 'Unsupported payment gateway selected.');
        }
    }
}
