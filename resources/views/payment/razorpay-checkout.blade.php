@extends('layouts.app')

@section('title', 'Razorpay Checkout')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3">Razorpay Test Checkout</h4>
                        <p class="mb-2"><strong>Student:</strong> {{ $student->name }}</p>
                        <p class="mb-2"><strong>Amount:</strong> Rs. {{ number_format((float) $feeLog->amount, 2) }}</p>
                        <p class="mb-4"><strong>Order ID:</strong> {{ $order['id'] }}</p>
                        <button id="pay-now-btn" class="btn btn-primary">Pay Now</button>
                        <a href="{{ route('student.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="failed-payment-form" method="POST" action="{{ route('razorpay.callback', [$student->id, $feeLog->id]) }}" style="display:none;">
        @csrf
        <input type="hidden" name="razorpay_status" value="failed">
        <input type="hidden" name="error_code" id="error_code">
        <input type="hidden" name="error_description" id="error_description">
    </form>
@endsection

@push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        (function () {
            const options = {
                key: @json($razorpayKey),
                amount: @json($amountInPaise),
                currency: 'INR',
                name: 'Student Fee Payment',
                description: 'Fee Payment for {{ $student->name }}',
                order_id: @json($order['id']),
                callback_url: @json(route('razorpay.callback', [$student->id, $feeLog->id])),
                prefill: {
                    name: @json($student->name),
                    email: @json($student->email),
                    contact: @json($student->phone),
                },
                theme: {
                    color: '#1b4d3e'
                },
                modal: {
                    ondismiss: function () {
                        document.getElementById('failed-payment-form').submit();
                    }
                }
            };

            const razorpay = new Razorpay(options);

            razorpay.on('payment.failed', function (response) {
                document.getElementById('error_code').value = response.error.code || '';
                document.getElementById('error_description').value = response.error.description || 'Payment failed';
                document.getElementById('failed-payment-form').submit();
            });

            document.getElementById('pay-now-btn').addEventListener('click', function (event) {
                event.preventDefault();
                razorpay.open();
            });

            window.addEventListener('load', function () {
                razorpay.open();
            });
        })();
    </script>
@endpush
