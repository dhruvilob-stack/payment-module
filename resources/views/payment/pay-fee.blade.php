@extends('layouts.app')

@section('title', 'Edit Student')

@push('styles')
    <style>
        :root {
            --bg: #f6f4ef;
            --surface: #ffffff;
            --ink: #1b1f24;
            --muted: #6b7280;
            --primary: #1b4d3e;
            --primary-ink: #ffffff;
            --danger: #9b1c1c;
            --border: #e5e7eb;
            --shadow: 0 18px 35px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Garamond", "Georgia", serif;
            color: var(--ink);
            background: radial-gradient(circle at top left, #e9f0e6 0%, #f6f4ef 45%, #f3efe7 100%);
        }

        .page {
            max-width: 960px;
            margin: 48px auto 72px;
            padding: 0 20px;
        }

        .card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 28px;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 32px;
        }

        p {
            margin: 0 0 24px;
            color: var(--muted);
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--muted);
            margin-bottom: 6px;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-family: "Helvetica Neue", "Arial", sans-serif;
            font-size: 14px;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .span-2 {
            grid-column: span 2;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            border: none;
            background: var(--primary);
            color: var(--primary-ink);
            padding: 12px 18px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            border-radius: 999px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 10px 18px rgba(27, 77, 62, 0.18);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .ghost {
            border: 1px solid var(--border);
            background: transparent;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 13px;
            text-decoration: none;
            color: inherit;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        .error-box {
            padding: 12px 16px;
            border-radius: 12px;
            background: #fee2e2;
            color: #991b1b;
            font-family: "Helvetica Neue", "Arial", sans-serif;
            margin-bottom: 16px;
        }

        .error {
            color: #991b1b;
            font-size: 12px;
            margin-top: 6px;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        @media (max-width: 720px) {
            .span-2 {
                grid-column: span 1;
            }

            .actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page">
        <form action="{{ route('payment.gateway', $data->id) }}" method="POST">
            @csrf
            <div class="card">
                <h1>Pay Fee</h1>
                <table class="table table-bordered">
                    <tr>
                        <th>Student Name</th>
                        <td>{{ $data->name }}</td>
                    </tr>
                    <tr>
                        <th>Student DOB</th>
                        <td>{{ date('d/m/Y', strtotime($data->dob)) }}</td>
                    </tr>
                    <tr>
                        <th>Student Fees</th>
                        <td>Rs. 5000 </td>

                    </tr>
                    <tr>
                        <th>Payment Gateway </th>
                        <td>
                            <select name="gateway" id="" class="form-select">
                                <option value="" disabled selected>Select Payment Gateway</option>
                                <option value="phonepe">PhonePe</option>
                                <option value="razorpay">Razorpay</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-center"><button type="submit" class="btn btn-primary">Pay
                                Fees</button></th>
                    </tr>
                </table>
            </div>
        </form>
    </div>
@endsection
