@extends('layouts.app')
@section('title', 'Payment History')
@section('page_title', 'Payments')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Payment History</div>
        <div class="page-subtitle">All recorded supplier & customer payments</div>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">+ Record Payment</a>
</div>

<form method="GET" action="{{ route('payments.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" style="min-width:170px;">
                <option value="">All Types</option>
                <option value="supplier_payment" {{ request('type') === 'supplier_payment' ? 'selected' : '' }}>Supplier Payments</option>
                <option value="customer_payment" {{ request('type') === 'customer_payment' ? 'selected' : '' }}>Customer Payments</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Method</label>
            <select name="method" class="form-select" style="min-width:130px;">
                <option value="">All Methods</option>
                <option value="cash"   {{ request('method') === 'cash'   ? 'selected' : '' }}>Cash</option>
                <option value="bank"   {{ request('method') === 'bank'   ? 'selected' : '' }}>Bank</option>
                <option value="card"   {{ request('method') === 'card'   ? 'selected' : '' }}>Card</option>
                <option value="online" {{ request('method') === 'online' ? 'selected' : '' }}>Online</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="form-group">
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Reference (Invoice)</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference No.</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="text-muted">{{ $payment->id }}</td>
                    <td class="text-muted" style="white-space:nowrap;">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    <td>
                        @if($payment->type === 'supplier_payment')
                            <span class="badge badge-primary">Supplier</span>
                        @else
                            <span class="badge badge-success">Customer</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->purchase)
                            <a href="{{ route('purchases.show', $payment->purchase) }}" class="text-primary fw-semibold">
                                {{ $payment->purchase->invoice_number }}
                            </a>
                        @elseif($payment->sale)
                            <a href="{{ route('sales.show', $payment->sale) }}" class="text-primary fw-semibold">
                                {{ $payment->sale->invoice_number }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="fw-semibold text-success">Rs. {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td class="text-muted">{{ $payment->reference_number ?? '—' }}</td>
                    <td>{{ $payment->user?->name ?? '—' }}</td>
                    <td class="text-muted text-sm">{{ $payment->notes ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">💰</div>
                        <div class="empty-state-text">No payments recorded yet</div>
                        <div class="empty-state-sub"><a href="{{ route('payments.create') }}" class="text-primary">Record your first payment</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="pagination-wrapper">{{ $payments->links() }}</div>
    @endif
</div>

@endsection