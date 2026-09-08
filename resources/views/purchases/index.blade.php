@extends('layouts.app')
@section('title', 'Purchases')
@section('page_title', 'Purchases')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Purchases</div>
        <div class="page-subtitle">Track all incoming inventory purchases</div>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Outstanding</th>
                    <th>Status</th>
                    <th>Actions</th>
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
                    <td class="fw-semibold">Rs. {{ number_format($purchase->total_amount, 0) }}</td>
                    <td class="text-success">Rs. {{ number_format($purchase->paid_amount, 0) }}</td>
                    <td class="{{ $purchase->outstanding_amount > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                        Rs. {{ number_format($purchase->outstanding_amount, 0) }}
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
                    <td>
                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">📥</div>
                        <div class="empty-state-text">No purchases found</div>
                        <div class="empty-state-sub"><a href="{{ route('purchases.create') }}" class="text-primary">Record your first purchase</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="pagination-wrapper">{{ $purchases->links() }}</div>
    @endif
</div>

@endsection