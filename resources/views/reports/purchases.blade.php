@extends('layouts.app')
@section('title', 'Purchase Report')
@section('page_title', 'Purchase Report')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📉 Purchase Report</div>
        <div class="page-subtitle">{{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}</div>
    </div>
    <button onclick="window.print()" class="btn btn-secondary no-print">🖨️ Print</button>
</div>

<form method="GET" action="{{ route('reports.purchases') }}" class="no-print">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to', $dateTo->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select" style="min-width:160px;">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="payment_status" class="form-select" style="min-width:130px;">
                <option value="">All</option>
                <option value="paid"    {{ request('payment_status') === 'paid'    ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="unpaid"  {{ request('payment_status') === 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Generate</button>
                <a href="{{ route('reports.purchases') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📄</div>
        <div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $summary['count'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">🛒</div>
        <div>
            <div class="stat-label">Total Purchased</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['total_purchased'], 0) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-label">Amount Paid</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['total_paid'], 0) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">💳</div>
        <div>
            <div class="stat-label">Outstanding</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['outstanding'], 0) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Purchase Transactions</span>
        <span class="badge badge-primary">{{ $summary['count'] }} records</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Outstanding</th>
                    <th>Status</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td class="fw-semibold text-primary">
                        <a href="{{ route('purchases.show', $purchase) }}">{{ $purchase->invoice_number }}</a>
                    </td>
                    <td>{{ $purchase->supplier?->name ?? '—' }}</td>
                    <td class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($purchase->total_amount, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($purchase->paid_amount, 0) }}</td>
                    <td class="text-right {{ max(0,$purchase->total_amount-$purchase->paid_amount) > 0 ? 'text-danger' : 'text-muted' }}">
                        Rs. {{ number_format(max(0,$purchase->total_amount-$purchase->paid_amount), 0) }}
                    </td>
                    <td>
                        @if($purchase->payment_status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($purchase->payment_status === 'partial')
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $purchase->user?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">📉</div>
                        <div class="empty-state-text">No purchases in this period</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
            @if($purchases->count())
            <tfoot>
                <tr style="background:var(--gray-50);font-weight:700;">
                    <td colspan="3" class="fw-bold">Totals</td>
                    <td class="text-right">Rs. {{ number_format($summary['total_purchased'], 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($summary['total_paid'], 0) }}</td>
                    <td class="text-right text-danger">Rs. {{ number_format($summary['outstanding'], 0) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
