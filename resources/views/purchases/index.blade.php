<!DOCTYPE html>
<html>
<head>
    <title>Purchases</title>
</head>
<body>

<h1>Purchases</h1>

@if(session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif

<a href="{{ route('purchases.create') }}">+ Create New Purchase</a>

<br><br>

<table border="1" cellpadding="10" cellspacing="0">

    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        @forelse($purchases as $purchase)

            <tr>
                <td>{{ $purchase->id }}</td>

                <td>{{ $purchase->invoice_number }}</td>

                <td>
                    {{ $purchase->supplier?->name ?? 'Unknown' }}
                </td>

                <td>{{ $purchase->purchase_date }}</td>

                <td>
                    {{ number_format($purchase->total_amount, 2) }}
                </td>

                <td>
                    {{ number_format($purchase->paid_amount, 2) }}
                </td>

                <td>
                    {{ ucfirst($purchase->payment_status) }}
                </td>

                <td>
                    <a href="{{ route('purchases.show', $purchase) }}">
                        View
                    </a>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="8">
                    No purchases found.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

<br>

{{ $purchases->links() }}

</body>
</html>