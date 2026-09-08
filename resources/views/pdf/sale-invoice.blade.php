<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $sale->invoice_number }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#1a1a2e; background:#fff; }

.page { padding:40px; }

/* Header */
.header { display:table; width:100%; margin-bottom:30px; }
.header-left { display:table-cell; vertical-align:top; }
.header-right { display:table-cell; text-align:right; vertical-align:top; }
.brand { font-size:24px; font-weight:700; color:#4f46e5; }
.brand-sub { font-size:11px; color:#64748b; margin-top:2px; }
.invoice-title { font-size:28px; font-weight:800; color:#1a1a2e; }
.invoice-meta { font-size:11px; color:#475569; margin-top:6px; line-height:1.8; }

/* Parties */
.parties { display:table; width:100%; margin:24px 0; }
.party { display:table-cell; width:48%; vertical-align:top; }
.party-right { display:table-cell; width:48%; text-align:right; vertical-align:top; }
.party-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:6px; }
.party-name { font-size:15px; font-weight:700; color:#1a1a2e; }
.party-detail { font-size:11px; color:#64748b; margin-top:3px; line-height:1.6; }

/* Status badge */
.badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:10px; font-weight:700; }
.badge-paid    { background:#dcfce7; color:#166534; }
.badge-partial { background:#fef9c3; color:#854d0e; }
.badge-unpaid  { background:#fee2e2; color:#991b1b; }

/* Table */
.items-table { width:100%; border-collapse:collapse; margin:20px 0; }
.items-table th { background:#f8fafc; border-bottom:2px solid #e2e8f0; padding:10px 8px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b; }
.items-table td { padding:10px 8px; border-bottom:1px solid #f1f5f9; font-size:12px; vertical-align:middle; }
.items-table tr:last-child td { border-bottom:none; }
.text-right { text-align:right; }
.text-center { text-align:center; }
.fw-bold { font-weight:700; }
.sku { font-size:10px; color:#94a3b8; font-family:Courier New, monospace; }

/* Totals */
.totals-box { float:right; width:260px; margin-top:10px; }
.total-row { display:table; width:100%; padding:6px 0; border-bottom:1px solid #f1f5f9; }
.total-label { display:table-cell; color:#64748b; font-size:11px; }
.total-value { display:table-cell; text-align:right; font-size:12px; font-weight:600; }
.grand-row { display:table; width:100%; padding:10px 0; background:#f8fafc; }
.grand-label { display:table-cell; font-size:14px; font-weight:800; padding:0 8px; }
.grand-value { display:table-cell; text-align:right; font-size:16px; font-weight:800; color:#4f46e5; padding:0 8px; }
.text-green { color:#16a34a; }
.text-red { color:#dc2626; }

/* Footer */
.clearfix::after { content:''; display:table; clear:both; }
.divider { border:none; border-top:1px solid #e2e8f0; margin:24px 0; }
.footer-text { font-size:10px; color:#94a3b8; text-align:center; margin-top:20px; }
.stamp { float:right; text-align:center; margin-top:40px; }
.stamp-box { width:140px; padding:10px; border:1px dashed #e2e8f0; border-radius:4px; }
.stamp-label { font-size:9px; color:#94a3b8; margin-bottom:20px; }
.stamp-line { border-top:1px solid #94a3b8; margin-top:20px; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">{{ \App\Models\Setting::get('business_name', 'BizManager') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_tagline', 'Business Management System') }}</div>
            <div class="brand-sub" style="margin-top:6px;">{{ \App\Models\Setting::get('business_phone', '') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_email', '') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_address', '') }}</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-meta">
                <strong>Invoice No.:</strong> {{ $sale->invoice_number }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}<br>
                <strong>Cashier:</strong> {{ $sale->user?->name ?? '—' }}
            </div>
        </div>
    </div>

    <hr class="divider">

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</div>
            @if($sale->customer?->phone)
                <div class="party-detail">Phone: {{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer?->email)
                <div class="party-detail">Email: {{ $sale->customer->email }}</div>
            @endif
            @if($sale->customer?->address)
                <div class="party-detail">{{ $sale->customer->address }}</div>
            @endif
        </div>
        <div class="party-right">
            <div class="party-label">Payment Status</div>
            @if($sale->payment_status === 'paid')
                <span class="badge badge-paid">PAID</span>
            @elseif($sale->payment_status === 'partial')
                <span class="badge badge-partial">PARTIALLY PAID</span>
            @else
                <span class="badge badge-unpaid">UNPAID</span>
            @endif
            <div class="party-detail" style="margin-top:6px;">Method: {{ ucfirst($sale->payment_method ?? 'cash') }}</div>
        </div>
    </div>

    {{-- Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $item->product->name }}</td>
                <td><span class="sku">{{ $item->product->sku }}</span></td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right text-red">{{ $item->discount > 0 ? '-'. number_format($item->discount, 2) : '—' }}</td>
                <td class="text-right fw-bold">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="clearfix">
        <div class="totals-box">
            <div class="total-row">
                <span class="total-label">Items Total</span>
                <span class="total-value">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($sale->items->sum('subtotal'), 2) }}</span>
            </div>
            @if($sale->discount > 0)
            <div class="total-row">
                <span class="total-label text-red">Discount</span>
                <span class="total-value text-red">-{{ number_format($sale->discount, 2) }}</span>
            </div>
            @endif
            @if($sale->tax > 0)
            <div class="total-row">
                <span class="total-label">Tax</span>
                <span class="total-value">{{ number_format($sale->tax, 2) }}</span>
            </div>
            @endif
            <div class="grand-row">
                <span class="grand-label">Grand Total</span>
                <span class="grand-value">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($sale->total_amount, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label text-green">Amount Paid</span>
                <span class="total-value text-green">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if($sale->total_amount > $sale->paid_amount)
            <div class="total-row">
                <span class="total-label text-red">Balance Due</span>
                <span class="total-value text-red">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($sale->total_amount - $sale->paid_amount, 2) }}</span>
            </div>
            @endif
        </div>
    </div>

    <hr class="divider" style="margin-top:80px;">

    <div class="footer-text">
        {{ \App\Models\Setting::get('invoice_footer', 'Thank you for your business!') }}
    </div>

</div>
</body>
</html>
