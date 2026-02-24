@extends('layouts.app')

@section('title', 'Students')

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

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Garamond", "Georgia", serif;
            color: var(--ink);
            background: radial-gradient(circle at top left, #e9f0e6 0%, #f6f4ef 45%, #f3efe7 100%);
        }

        .page {
            max-width: 1100px;
            margin: 48px auto 72px;
            padding: 0 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .title-group h1 {
            margin: 0 0 6px;
            font-size: 36px;
            letter-spacing: 0.5px;
        }

        .title-group p {
            margin: 0;
            color: var(--muted);
            font-family: "Helvetica Neue", "Arial", sans-serif;
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

        .button:hover { transform: translateY(-2px); }

        .table-card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 20px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        thead {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1.2px;
            color: var(--muted);
        }

        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
        }

        tbody tr:hover { background: #f8f7f3; }

        .actions {
            display: flex;
            gap: 8px;
        }

        .ghost {
            border: 1px solid var(--border);
            background: transparent;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .danger { color: var(--danger); border-color: rgba(155, 28, 28, 0.3); }

        .muted {
            color: var(--muted);
            text-align: center;
            padding: 32px 0;
        }

        .flash {
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #e9f0e6;
            color: #1b4d3e;
            font-family: "Helvetica Neue", "Arial", sans-serif;
        }

        .toast-wrap {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 1080;
        }

        @media (max-width: 720px) {
            .header { flex-direction: column; align-items: flex-start; }
            th, td { padding: 12px 8px; }
            .actions { flex-direction: column; align-items: flex-start; }
        }
    </style>
@endpush

@section('content')
    @if(session('payment_success'))
        <div class="toast-wrap">
            <div id="paymentSuccessToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ session('payment_success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="page">
        <div class="header">
            <div class="title-group">
                <h1>Student Directory</h1>
                <p>Manage student records with quick add, edit, and delete actions.</p>
            </div>
            <a class="button" href="{{ route('student.create') }}">Add Student</a>
        </div>

        @if(session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Date of Birth</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone }}</td>
                            <td>{{ $student->dob }}</td>
                            <td>{{ $student->address }}</td>
                            <td>
                                <div class="actions">
                                    <a class="ghost" href="{{ route('student.edit', $student) }}">Edit</a>
                                    <form method="POST" action="{{ route('student.destroy', $student) }}" onsubmit="return confirm('Delete {{ $student->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ghost danger" type="submit">Delete</button>
                                    </form>
                                    <a href="{{ route('pay.fee', $student->id) }}" class="ghost btn btn-success btn-sm">Pay Fee</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="muted">No students yet. Add the first one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    @if(session('payment_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toastElement = document.getElementById('paymentSuccessToast');
                if (!toastElement) return;
                var toast = new bootstrap.Toast(toastElement, { delay: 3500 });
                toast.show();
            });
        </script>
    @endif
@endpush
