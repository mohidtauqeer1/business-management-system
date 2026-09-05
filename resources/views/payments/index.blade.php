<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
</head>
<body>

    <h1>Business Management System</h1>

    <h2>Payment History</h2>

    <p>
        Welcome, {{ auth()->user()->name }}
        |
        Role: {{ auth()->user()->role }}
    </p>

    <a href="{{ route('payments.create') }}">
        Record Payment
    </a>

    <hr>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="8">

        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Party</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Recorded By</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>

            @forelse($payments as $payment)

                <tr>

                    <td>
                        {{ $payment->created_at->format('d M Y H:i') }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $payment->type)) }}
                    </td>

                    <td>
                        @if($payment->supplier)
                            Supplier: {{ $payment->supplier->name }}
                        @elseif($payment->customer)
                            Customer: {{ $payment->customer->name }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        {{ $payment->amount }}
                    </td>

                    <td>
                        {{ ucfirst($payment->payment_method) }}
                    </td>

                    <td>
                        {{ $payment->reference_number ?? '-' }}
                    </td>

                    <td>
                        {{ $payment->user->name }}
                    </td>

                    <td>
                        {{ $payment->notes ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8">
                        No payments found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <br>

    {{ $payments->links() }}

</body>
</html>