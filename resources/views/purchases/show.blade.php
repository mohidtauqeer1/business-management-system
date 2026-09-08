@extends('layouts.app')
@section('title', 'Purchase ' . $purchase->invoice_number)
@section('page_title', 'Purchase Invoice')

@section('content')

<div class="page-header no-print">
    <div>
        <div class="page-title">{{ $purchase->invoice_number }}</div>
        <div class="page-subtitle">Purchase invoice details</div>
    </div>
    <div class="page-header-actions d-flex gap-8">
        <a href="{{ route('returns.purchases.create', $purchase) }}" class="btn btn-warning">🔄 Process Return</a>
        <a href="{{ route('purchases.pdf', $purchase) }}" class="btn btn-secondary" target="_blank">📄 Download PDF</a>
        <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">← Back</a>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
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
            <h2>PURCHASE</h2>
            <p><strong>Invoice:</strong> {{ $purchase->invoice_number }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
            <p><strong>Recorded By:</strong> {{ $purchase->user?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="invoice-parties">
        <div>
            <div class="invoice-party-label">From Supplier</div>
            <div class="invoice-party-name">{{ $purchase->supplier?->name ?? 'Unknown Supplier' }}</div>
            @if($purchase->supplier?->phone)
                <div class="invoice-party-detail">📞 {{ $purchase->supplier->phone }}</div>
            @endif
            @if($purchase->supplier?->email)
                <div class="invoice-party-detail">✉️ {{ $purchase->supplier->email }}</div>
            @endif
            @if($purchase->supplier?->address)
                <div class="invoice-party-detail">📍 {{ $purchase->supplier->address }}</div>
            @endif
        </div>
        <div>
            <div class="invoice-party-label">Payment Summary</div>
            <div class="invoice-party-name">
                @if($purchase->payment_status === 'paid')
                    <span class="badge badge-success">✅ Fully Paid</span>
                @elseif($purchase->payment_status === 'partial')
                    <span class="badge badge-warning">⏳ Partially Paid</span>
                @else
                    <span class="badge badge-danger">❌ Unpaid</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="table-wrapper">
        <table class="table">
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
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $item->product->name }}</td>
                    <td><code style="font-size:11px;background:var(--gray-100);padding:2px 5px;border-radius:3px;">{{ $item->product->sku }}</code></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-totals-box">
        <div class="invoice-totals-row">
            <span>Items Total</span>
            <span>Rs. {{ number_format($purchase->items->sum('subtotal'), 2) }}</span>
        </div>
        <div class="invoice-totals-row invoice-total-grand">
            <span>Grand Total</span>
            <span>Rs. {{ number_format($purchase->total_amount, 2) }}</span>
        </div>
        <div class="invoice-totals-row" style="color:var(--success-dark);">
            <span>Amount Paid</span>
            <span>Rs. {{ number_format($purchase->paid_amount, 2) }}</span>
        </div>
        <div class="invoice-totals-row {{ $purchase->outstanding_amount > 0 ? 'text-danger' : 'text-success' }}">
            <span>Outstanding</span>
            <span>Rs. {{ number_format($purchase->outstanding_amount, 2) }}</span>
        </div>
    </div>

    {{-- Payment History --}}
    @if($purchase->payments->count())
    <div style="margin-top:32px;">
        <h4 style="font-size:14px;font-weight:700;color:var(--gray-700);margin-bottom:12px;">Payment History</h4>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Recorded By</th></tr>
                </thead>
                <tbody>
                    @foreach($purchase->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="fw-semibold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td class="text-muted">{{ $payment->reference_number ?? '—' }}</td>
                        <td>{{ $payment->user?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="invoice-status">
        <p>Thank you for your business.</p>
    </div>

</div>

@endsection
