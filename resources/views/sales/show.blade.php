@extends('layouts.app')
@section('title', 'Sale ' . $sale->invoice_number)
@section('page_title', 'Sale Invoice')

@section('content')

<div class="page-header no-print">
    <div>
        <div class="page-title">{{ $sale->invoice_number }}</div>
        <div class="page-subtitle">Sales invoice details</div>
    </div>
    <div class="page-header-actions d-flex gap-8">
        <a href="{{ route('returns.sales.create', $sale) }}" class="btn btn-warning">↩️ Process Return</a>
        <a href="{{ route('sales.pdf', $sale) }}" class="btn btn-secondary" target="_blank">📄 Download PDF</a>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">← Back</a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">+ New Sale</a>
    </div>
</div>

<div class="invoice-page">

    <div class="invoice-header">
        <div>
            <div class="invoice-brand">{{ \App\Models\Setting::get('business_name', 'BizManager') }}</div>
            <div class="invoice-brand-sub">{{ \App\Models\Setting::get('business_tagline', 'Business Management System') }}</div>
            <div class="invoice-brand-sub">{{ \App\Models\Setting::get('business_phone') }}</div>
            <div class="invoice-brand-sub">{{ \App\Models\Setting::get('business_address') }}</div>
        </div>
        <div class="invoice-meta">
            <h2>INVOICE</h2>
            <p><strong>Invoice:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</p>
            <p><strong>Cashier:</strong> {{ $sale->user?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="invoice-parties">
        <div>
            <div class="invoice-party-label">Bill To</div>
            <div class="invoice-party-name">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</div>
            @if($sale->customer?->phone)
                <div class="invoice-party-detail">📞 {{ $sale->customer->phone }}</div>
            @endif
            @if($sale->customer?->email)
                <div class="invoice-party-detail">✉️ {{ $sale->customer->email }}</div>
            @endif
        </div>
        <div>
            <div class="invoice-party-label">Payment Status</div>
            <div class="invoice-party-name">
                @if($sale->payment_status === 'paid')
                    <span class="badge badge-success">✅ Fully Paid</span>
                @elseif($sale->payment_status === 'partial')
                    <span class="badge badge-warning">⏳ Partially Paid</span>
                @else
                    <span class="badge badge-danger">❌ Unpaid</span>
                @endif
            </div>
            <div class="invoice-party-detail">Method: {{ ucfirst(str_replace('_', ' ', $sale->payment_method ?? 'cash')) }}</div>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="table">
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
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $item->product->name }}</td>
                    <td><code style="font-size:11px;background:var(--gray-100);padding:2px 5px;border-radius:3px;">{{ $item->product->sku }}</code></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right text-danger">{{ $item->discount > 0 ? '-Rs. ' . number_format($item->discount, 2) : '—' }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-totals-box">
        <div class="invoice-totals-row">
            <span>Items Total</span>
            <span>Rs. {{ number_format($sale->items->sum('subtotal'), 2) }}</span>
        </div>
        @if($sale->discount > 0)
        <div class="invoice-totals-row text-danger">
            <span>Overall Discount</span>
            <span>-Rs. {{ number_format($sale->discount, 2) }}</span>
        </div>
        @endif
        @if($sale->tax > 0)
        <div class="invoice-totals-row">
            <span>Tax</span>
            <span>Rs. {{ number_format($sale->tax, 2) }}</span>
        </div>
        @endif
        <div class="invoice-totals-row invoice-total-grand">
            <span>Grand Total</span>
            <span>Rs. {{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="invoice-totals-row" style="color:var(--success-dark);">
            <span>Amount Paid</span>
            <span>Rs. {{ number_format($sale->paid_amount, 2) }}</span>
        </div>
        <div class="invoice-totals-row {{ max(0, $sale->total_amount - $sale->paid_amount) > 0 ? 'text-danger' : 'text-success' }}">
            <span>Outstanding</span>
            <span>Rs. {{ number_format(max(0, $sale->total_amount - $sale->paid_amount), 2) }}</span>
        </div>
    </div>

    @if($sale->payments->count())
    <div style="margin-top:32px;">
        <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:12px;">Payment History</h4>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="fw-semibold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td class="text-muted">{{ $payment->reference_number ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="invoice-status">
        <p>Thank you for your business!</p>
    </div>
</div>

@endsection
