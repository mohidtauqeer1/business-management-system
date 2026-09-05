<!DOCTYPE html>
<html>
<head>
    <title>{{ $supplier->name }}</title>
</head>
<body>

<h1>{{ $supplier->name }}</h1>

<p>
    <strong>Contact Person:</strong>
    {{ $supplier->contact_person ?? '-' }}
</p>

<p>
    <strong>Phone:</strong>
    {{ $supplier->phone ?? '-' }}
</p>

<p>
    <strong>Email:</strong>
    {{ $supplier->email ?? '-' }}
</p>

<p>
    <strong>Address:</strong>
    {{ $supplier->address ?? '-' }}
</p>

<hr>

<h2>Purchase History</h2>

@if($supplier->purchases->count())

<table border="1" cellpadding="10">

    <tr>
        <th>Invoice</th>
        <th>Date</th>
        <th>Total</th>
        <th>Status</th>
    </tr>

    @foreach($supplier->purchases as $purchase)

        <tr>
            <td>
                {{ $purchase->invoice_number }}
            </td>

            <td>
                {{ $purchase->purchase_date }}
            </td>

            <td>
                {{ number_format($purchase->total_amount, 2) }}
            </td>

            <td>
                {{ ucfirst($purchase->payment_status) }}
            </td>
        </tr>

    @endforeach

</table>

@else

<p>No purchase history.</p>

@endif

<br>

<a href="{{ route('suppliers.edit', $supplier) }}">
    Edit
</a>

|

<a href="{{ route('suppliers.index') }}">
    Back
</a>

</body>
</html>