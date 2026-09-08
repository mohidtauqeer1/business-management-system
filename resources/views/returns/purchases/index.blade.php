@extends('layouts.app')
@section('title', 'Purchase Returns')
@section('page_title', 'Purchase Returns')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🔄 Purchase Returns</div>
        <div class="page-subtitle">Items returned to suppliers</div>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">← Back to Purchases</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Return #</th>
                    <th>Original Invoice</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th class="text-right">Return Total</th>
                    <th class="text-right">Credit</th>
                    <th>Credit Status</th>
                    <th>Processed By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                <tr>
                    <td class="fw-semibold text-primary">
                        <a href="{{ route('returns.purchases.show', $return) }}">{{ $return->return_number }}</a>
                    </td>
                    <td>
                        <a href="{{ route('purchases.show', $return->purchase) }}" class="text-muted">
                            {{ $return->purchase->invoice_number }}
                        </a>
                    </td>
                    <td>{{ $return->purchase->supplier?->name ?? '—' }}</td>
                    <td class="text-muted">{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($return->total_amount, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($return->credit_amount, 0) }}</td>
                    <td>
                        @if($return->credit_status === 'credited')
                            <span class="badge badge-success">Credited</span>
                        @elseif($return->credit_status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-gray">No Credit</span>
                        @endif
                    </td>
                    <td>{{ $return->user?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('returns.purchases.show', $return) }}" class="btn btn-secondary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">🔄</div>
                        <div class="empty-state-text">No purchase returns found</div>
                        <div class="empty-state-sub">Returns are created from the <a href="{{ route('purchases.index') }}" class="text-primary">Purchases</a> page</div>
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
