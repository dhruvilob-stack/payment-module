<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PhonePePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('student', StudentController::class);

Route::get('pay/fee/{id}', [PaymentController::class, 'payFee'])->name('pay.fee'); 
Route::post('pay-gateway/{id}', [PaymentController::class, 'gateway'])->name('payment.gateway'); 

Route::get('phonepe/{id}/{logId}', [PhonePePaymentController::class, 'initialPayment'])->name('phonepe.gateway');
Route::post('phonepe/callback/{id}/{logId}', [PhonePePaymentController::class, 'callback'])->name('phonepe.callback');
Route::get('razorpay/{id}/{logId}', [RazorpayPaymentController::class, 'initialPayment'])->name('razorpay.gateway');
Route::post('razorpay/callback/{id}/{logId}', [RazorpayPaymentController::class, 'callback'])->name('razorpay.callback');
