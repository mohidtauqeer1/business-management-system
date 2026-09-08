@extends('layouts.app')
@section('title', 'Sales')
@section('page_title', 'Sales')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Sales</div>
        <div class="page-subtitle">Track all outgoing sales transactions</div>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary">+ New Sale</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                    <td class="fw-semibold">Rs. {{ number_format($sale->total_amount, 0) }}</td>
                    <td class="text-success">Rs. {{ number_format($sale->paid_amount, 0) }}</td>
                    <td class="{{ ($sale->total_amount - $sale->paid_amount) > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                        Rs. {{ number_format(max(0, $sale->total_amount - $sale->paid_amount), 0) }}
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
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">📤</div>
                        <div class="empty-state-text">No sales found</div>
                        <div class="empty-state-sub"><a href="{{ route('sales.create') }}" class="text-primary">Record your first sale</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
    <div class="pagination-wrapper">{{ $sales->links() }}</div>
    @endif
</div>

@endsection