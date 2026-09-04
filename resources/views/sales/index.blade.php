<!DOCTYPE html>
<html>
<head>
    <title>Sales</title>
</head>
<body>

<h1>Sales</h1>

@if(session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif

<a href="{{ route('sales.create') }}">+ Create New Sale</a>

<br><br>

<table border="1" cellpadding="10" cellspacing="0">

    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

        @forelse($sales as $sale)

            <tr>
                <td>{{ $sale->id }}</td>

                <td>
                    {{ $sale->invoice_number }}
                </td>

                <td>
                    {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                </td>

                <td>
                    {{ $sale->sale_date }}
                </td>

                <td>
                    {{ number_format($sale->total_amount, 2) }}
                </td>

                <td>
                    {{ number_format($sale->paid_amount, 2) }}
                </td>

                <td>
                    {{ ucfirst($sale->payment_status) }}
                </td>

                <td>
                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                </td>

                <td>
                    <a href="{{ route('sales.show', $sale) }}">
                        View
                    </a>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9">
                    No sales found.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

<br>

{{ $sales->links() }}

</body>
</html>