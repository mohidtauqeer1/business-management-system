<!DOCTYPE html>
<html>
<head>
    <title>{{ $customer->name }}</title>
</head>
<body>

<h1>{{ $customer->name }}</h1>

<p>
    <strong>Phone:</strong>
    {{ $customer->phone ?? '-' }}
</p>

<p>
    <strong>Email:</strong>
    {{ $customer->email ?? '-' }}
</p>

<p>
    <strong>Address:</strong>
    {{ $customer->address ?? '-' }}
</p>

<p>
    <strong>Credit Balance:</strong>
    {{ number_format($customer->credit_balance, 2) }}
</p>

<hr>

<h2>Sales History</h2>

@if($customer->sales->count())

<table border="1" cellpadding="10">

    <tr>
        <th>Invoice</th>
        <th>Date</th>
        <th>Total</th>
        <th>Paid</th>
        <th>Status</th>
    </tr>

    @foreach($customer->sales as $sale)

        <tr>

            <td>
                {{ $sale->invoice_number }}
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

        </tr>

    @endforeach

</table>

@else

<p>No sales history.</p>

@endif

<br>

<a href="{{ route('customers.edit', $customer) }}">
    Edit
</a>

|

<a href="{{ route('customers.index') }}">
    Back
</a>

</body>
</html>