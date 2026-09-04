<!DOCTYPE html>
<html>
<head>
    <title>Purchase {{ $purchase->invoice_number }}</title>

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

        .actions {
            margin-bottom: 25px;
        }

        @media print {
            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="invoice">

    <div class="actions">

        <a href="{{ route('purchases.index') }}">
            ← Back to Purchases
        </a>

        &nbsp;&nbsp;

        <a href="{{ route('purchases.create') }}">
            + New Purchase
        </a>

        &nbsp;&nbsp;

        <button onclick="window.print()">
            Print Purchase Invoice
        </button>

    </div>

    <div class="header">

        <div>
            <h1>PURCHASE INVOICE</h1>

            <p>
                <strong>Invoice:</strong>
                {{ $purchase->invoice_number }}
            </p>

            <p>
                <strong>Date:</strong>
                {{ $purchase->purchase_date }}
            </p>
        </div>

        <div>

            <p>
                <strong>Supplier:</strong>
                {{ $purchase->supplier?->name ?? 'Unknown' }}
            </p>

            @if($purchase->supplier)
                <p>
                    <strong>Phone:</strong>
                    {{ $purchase->supplier->phone }}
                </p>
            @endif

            <p>
                <strong>Recorded By:</strong>
                {{ $purchase->user?->name ?? 'Unknown' }}
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
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

            @foreach($purchase->items as $index => $item)

                <tr>

                    <td>{{ $index + 1 }}</td>

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
                {{ number_format($purchase->items->sum('subtotal'), 2) }}
            </span>
        </p>

        <p class="grand-total">
            <span>Total:</span>

            <span>
                {{ number_format($purchase->total_amount, 2) }}
            </span>
        </p>

        <p>
            <span>Paid:</span>

            <span>
                {{ number_format($purchase->paid_amount, 2) }}
            </span>
        </p>

        <p>
            <span>Payment Status:</span>

            <strong>
                {{ ucfirst($purchase->payment_status) }}
            </strong>
        </p>

    </div>

    <br><br>

    <p style="text-align:center;">
        Purchase recorded successfully.
    </p>

</div>

</body>
</html>