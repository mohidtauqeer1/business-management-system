<!DOCTYPE html>

<html>
<head>
    <title>Sale {{ $sale->invoice_number }}</title>

```
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
    }

    .invoice {
        max-width: 900px;
        margin: auto;
    }

    .header {
        display: flex;
        justify-content: space-between;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 10px;
        text-align: left;
    }

    .totals {
        width: 350px;
        margin-left: auto;
        margin-top: 25px;
    }

    .totals p {
        display: flex;
        justify-content: space-between;
    }

    .grand-total {
        font-size: 20px;
        font-weight: bold;
    }

    .payment-info {
        width: 350px;
        margin-left: auto;
        margin-top: 35px;
    }

    .payment-info h3 {
        margin-bottom: 15px;
    }

    .payment-info p {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
    }

    .payment-history {
        margin-top: 30px;
    }

    .actions {
        margin-bottom: 25px;
    }

    @media print {
        .actions {
            display: none;
        }
    }
</style>
```

</head>

<body>

<div class="invoice">

```
<div class="actions">

    <a href="{{ route('sales.index') }}">
        ← Back to Sales
    </a>

    &nbsp;&nbsp;

    <a href="{{ route('sales.create') }}">
        + New Sale
    </a>

    &nbsp;&nbsp;

    <button onclick="window.print()">
        Print Invoice
    </button>

</div>


<div class="header">

    <div>
        <h1>SALE INVOICE</h1>

        <p>
            <strong>Invoice:</strong>
            {{ $sale->invoice_number }}
        </p>

        <p>
            <strong>Date:</strong>
            {{ $sale->sale_date }}
        </p>
    </div>


    <div>

        <p>
            <strong>Customer:</strong>
            {{ $sale->customer?->name ?? 'Walk-in Customer' }}
        </p>

        @if($sale->customer)
            <p>
                <strong>Phone:</strong>
                {{ $sale->customer->phone }}
            </p>
        @endif

        <p>
            <strong>Cashier:</strong>
            {{ $sale->user?->name ?? 'Unknown' }}
        </p>

    </div>

</div>


<table>

    <thead>

        <tr>
            <th>#</th>
            <th>Product</th>
            <th>SKU</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Discount</th>
            <th>Subtotal</th>
        </tr>

    </thead>


    <tbody>

        @foreach($sale->items as $index => $item)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $item->product->name }}
                </td>

                <td>
                    {{ $item->product->sku }}
                </td>

                <td>
                    {{ $item->quantity }}
                </td>

                <td>
                    {{ number_format($item->unit_price, 2) }}
                </td>

                <td>
                    {{ number_format($item->discount, 2) }}
                </td>

                <td>
                    {{ number_format($item->subtotal, 2) }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>


<div class="totals">

    <p>
        <span>Items Total:</span>
        <span>
            {{ number_format(
                $sale->items->sum('subtotal'),
                2
            ) }}
        </span>
    </p>

    <p>
        <span>Discount:</span>
        <span>
            {{ number_format($sale->discount, 2) }}
        </span>
    </p>

    <p>
        <span>Tax:</span>
        <span>
            {{ number_format($sale->tax, 2) }}
        </span>
    </p>

    <p class="grand-total">
        <span>Grand Total:</span>
        <span>
            {{ number_format($sale->total_amount, 2) }}
        </span>
    </p>

    <p>
        <span>Paid:</span>
        <span>
            {{ number_format($sale->paid_amount, 2) }}
        </span>
    </p>

    <p>
        <span>Payment Status:</span>
        <strong>
            {{ ucfirst($sale->payment_status) }}
        </strong>
    </p>

    <p>
        <span>Payment Method:</span>
        <span>
            {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
        </span>
    </p>

</div>


<!-- Payment Information -->

<div class="payment-info">

    <h3>Payment Information</h3>

    <p>
        <span>Total:</span>

        <span>
            {{ number_format($sale->total_amount, 2) }}
        </span>
    </p>

    <p>
        <span>Paid:</span>

        <span>
            {{ number_format($sale->paid_amount, 2) }}
        </span>
    </p>

    <p>
        <span>Outstanding:</span>

        <span>
            {{ number_format(
                max(
                    0,
                    $sale->total_amount - $sale->paid_amount
                ),
                2
            ) }}
        </span>
    </p>

    <p>
        <span>Status:</span>

        <strong>
            {{ ucfirst($sale->payment_status) }}
        </strong>
    </p>

</div>


<!-- Payment History -->

@if($sale->payments->count())

    <div class="payment-history">

        <h4>Payment History</h4>

        <table>

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                </tr>
            </thead>

            <tbody>

                @foreach($sale->payments as $payment)

                    <tr>

                        <td>
                            {{ $payment->created_at->format('d M Y H:i') }}
                        </td>

                        <td>
                            {{ number_format($payment->amount, 2) }}
                        </td>

                        <td>
                            {{ ucfirst($payment->payment_method) }}
                        </td>

                        <td>
                            {{ $payment->reference_number ?? '-' }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

@endif


<br><br>

<p style="text-align:center;">
    Thank you for your business!
</p>
```

</div>

</body>
</html>
