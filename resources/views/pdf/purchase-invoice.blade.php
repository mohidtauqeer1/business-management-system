<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Purchase {{ $purchase->invoice_number }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size:12px; color:#1a1a2e; background:#fff; }
.page { padding:40px; }
.header { display:table; width:100%; margin-bottom:30px; }
.header-left { display:table-cell; vertical-align:top; }
.header-right { display:table-cell; text-align:right; vertical-align:top; }
.brand { font-size:24px; font-weight:700; color:#0891b2; }
.brand-sub { font-size:11px; color:#64748b; margin-top:2px; }
.invoice-title { font-size:28px; font-weight:800; color:#1a1a2e; }
.invoice-meta { font-size:11px; color:#475569; margin-top:6px; line-height:1.8; }
.parties { display:table; width:100%; margin:24px 0; }
.party { display:table-cell; width:48%; vertical-align:top; }
.party-right { display:table-cell; width:48%; text-align:right; vertical-align:top; }
.party-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:6px; }
.party-name { font-size:15px; font-weight:700; color:#1a1a2e; }
.party-detail { font-size:11px; color:#64748b; margin-top:3px; line-height:1.6; }
.badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:10px; font-weight:700; }
.badge-paid { background:#dcfce7; color:#166534; }
.badge-partial { background:#fef9c3; color:#854d0e; }
.badge-unpaid { background:#fee2e2; color:#991b1b; }
.items-table { width:100%; border-collapse:collapse; margin:20px 0; }
.items-table th { background:#f8fafc; border-bottom:2px solid #e2e8f0; padding:10px 8px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#64748b; }
.items-table td { padding:10px 8px; border-bottom:1px solid #f1f5f9; font-size:12px; }
.items-table tr:last-child td { border-bottom:none; }
.text-right { text-align:right; }
.text-center { text-align:center; }
.fw-bold { font-weight:700; }
.sku { font-size:10px; color:#94a3b8; font-family:Courier New, monospace; }
.totals-box { float:right; width:260px; margin-top:10px; }
.total-row { display:table; width:100%; padding:6px 0; border-bottom:1px solid #f1f5f9; }
.total-label { display:table-cell; color:#64748b; font-size:11px; }
.total-value { display:table-cell; text-align:right; font-size:12px; font-weight:600; }
.grand-row { display:table; width:100%; padding:10px 0; background:#f8fafc; }
.grand-label { display:table-cell; font-size:14px; font-weight:800; padding:0 8px; }
.grand-value { display:table-cell; text-align:right; font-size:16px; font-weight:800; color:#0891b2; padding:0 8px; }
.text-green { color:#16a34a; }
.text-red { color:#dc2626; }
.clearfix::after { content:''; display:table; clear:both; }
.divider { border:none; border-top:1px solid #e2e8f0; margin:24px 0; }
.footer-text { font-size:10px; color:#94a3b8; text-align:center; margin-top:20px; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-left">
            <div class="brand">{{ \App\Models\Setting::get('business_name', 'BizManager') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_tagline', 'Business Management System') }}</div>
            <div class="brand-sub" style="margin-top:6px;">{{ \App\Models\Setting::get('business_phone', '') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_email', '') }}</div>
            <div class="brand-sub">{{ \App\Models\Setting::get('business_address', '') }}</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">PURCHASE ORDER</div>
            <div class="invoice-meta">
                <strong>Invoice No.:</strong> {{ $purchase->invoice_number }}<br>
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}<br>
                <strong>Recorded By:</strong> {{ $purchase->user?->name ?? '—' }}
            </div>
        </div>
    </div>

    <hr class="divider">

    <div class="parties">
        <div class="party">
            <div class="party-label">From Supplier</div>
            <div class="party-name">{{ $purchase->supplier?->name ?? '—' }}</div>
            @if($purchase->supplier?->contact_person)
                <div class="party-detail">Contact: {{ $purchase->supplier->contact_person }}</div>
            @endif
            @if($purchase->supplier?->phone)
                <div class="party-detail">Phone: {{ $purchase->supplier->phone }}</div>
            @endif
            @if($purchase->supplier?->address)
                <div class="party-detail">{{ $purchase->supplier->address }}</div>
            @endif
        </div>
        <div class="party-right">
            <div class="party-label">Payment Status</div>
            @if($purchase->payment_status === 'paid')
                <span class="badge badge-paid">PAID</span>
            @elseif($purchase->payment_status === 'partial')
                <span class="badge badge-partial">PARTIALLY PAID</span>
            @else
                <span class="badge badge-unpaid">UNPAID</span>
            @endif
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>SKU</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $item->product->name }}</td>
                <td><span class="sku">{{ $item->product->sku }}</span></td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right fw-bold">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="totals-box">
            <div class="grand-row">
                <span class="grand-label">Grand Total</span>
                <span class="grand-value">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($purchase->total_amount, 2) }}</span>
            </div>
            <div class="total-row">
                <span class="total-label text-green">Amount Paid</span>
                <span class="total-value text-green">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($purchase->paid_amount, 2) }}</span>
            </div>
            @if($purchase->outstanding_amount > 0)
            <div class="total-row">
                <span class="total-label text-red">Outstanding</span>
                <span class="total-value text-red">{{ \App\Models\Setting::get('currency_symbol','Rs.') }} {{ number_format($purchase->outstanding_amount, 2) }}</span>
            </div>
            @endif
        </div>
    </div>

    <hr class="divider" style="margin-top:80px;">
    <div class="footer-text">{{ \App\Models\Setting::get('invoice_footer', 'Thank you for your business!') }}</div>

</div>
</body>
</html>
