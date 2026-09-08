@extends('layouts.app')
@section('title', 'Sales Report')
@section('page_title', 'Sales Report')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📈 Sales Report</div>
        <div class="page-subtitle">
            {{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}
        </div>
    </div>
    <button onclick="window.print()" class="btn btn-secondary no-print">🖨️ Print</button>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('reports.sales') }}" class="no-print">
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
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select" style="min-width:160px;">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

{{-- Summary Cards --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📄</div>
        <div>
            <div class="stat-label">Total Invoices</div>
            <div class="stat-value">{{ $summary['count'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">💰</div>
        <div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['total_sales'], 0) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">✅</div>
        <div>
            <div class="stat-label">Amount Received</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['total_received'], 0) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">⏳</div>
        <div>
            <div class="stat-label">Outstanding</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($summary['outstanding'], 0) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Sales Transactions</span>
        <span class="badge badge-primary">{{ $summary['count'] }} records</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Outstanding</th>
                    <th>Status</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td class="fw-semibold text-primary">
                        <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                    </td>
                    <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                    <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($sale->total_amount, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($sale->paid_amount, 0) }}</td>
                    <td class="text-right {{ max(0,$sale->total_amount-$sale->paid_amount) > 0 ? 'text-danger' : 'text-muted' }}">
                        Rs. {{ number_format(max(0,$sale->total_amount-$sale->paid_amount), 0) }}
                    </td>
                    <td>
                        @if($sale->payment_status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $sale->user?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">📈</div>
                        <div class="empty-state-text">No sales in this period</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
            @if($sales->count())
            <tfoot>
                <tr style="background:var(--gray-50);font-weight:700;">
                    <td colspan="3" class="fw-bold">Totals</td>
                    <td class="text-right">Rs. {{ number_format($summary['total_sales'], 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($summary['total_received'], 0) }}</td>
                    <td class="text-right text-danger">Rs. {{ number_format($summary['outstanding'], 0) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
