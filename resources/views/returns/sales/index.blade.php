@extends('layouts.app')
@section('title', 'Sale Returns')
@section('page_title', 'Sale Returns')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">↩️ Sale Returns</div>
        <div class="page-subtitle">All customer returns and refunds</div>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">← Back to Sales</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Return #</th>
                    <th>Original Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-right">Return Total</th>
                    <th class="text-right">Refund</th>
                    <th>Refund Status</th>
                    <th>Processed By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td class="fw-semibold text-primary">
                        <a href="{{ route('returns.sales.show', $return) }}">{{ $return->return_number }}</a>
                    </td>
                    <td>
                        <a href="{{ route('sales.show', $return->sale) }}" class="text-muted">
                            {{ $return->sale->invoice_number }}
                        </a>
                    </td>
                    <td>{{ $return->sale->customer?->name ?? 'Walk-in' }}</td>
                    <td class="text-muted">{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($return->total_amount, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($return->refund_amount, 0) }}</td>
                    <td>
                        @if($return->refund_status === 'refunded')
                            <span class="badge badge-success">Refunded</span>
                        @elseif($return->refund_status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-gray">No Refund</span>
                        @endif
                    </td>
                    <td>{{ $return->user?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('returns.sales.show', $return) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">↩️</div>
                        <div class="empty-state-text">No sale returns found</div>
                        <div class="empty-state-sub">Returns are created from the <a href="{{ route('sales.index') }}" class="text-primary">Sales</a> page</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
    <div class="pagination-wrapper">{{ $returns->links() }}</div>
    @endif
</div>

@endsection
