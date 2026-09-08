@extends('layouts.app')
@section('title', 'Supplier Balances')
@section('page_title', 'Supplier Balances')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Supplier Balances</div>
        <div class="page-subtitle">Outstanding payable amounts owed to suppliers</div>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">+ Record Payment</a>
</div>

{{-- Summary Cards --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">🏭</div>
        <div>
            <div class="stat-label">Total Suppliers</div>
            <div class="stat-value">{{ $suppliers->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">💳</div>
        <div>
            <div class="stat-label">Total Payable</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($suppliers->sum('outstanding'), 0) }}
            </div>
            <div class="stat-sub">Across {{ $suppliers->where('outstanding', '>', 0)->count() }} suppliers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-label">Total Paid</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($suppliers->sum('total_paid'), 0) }}
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">📥</div>
        <div>
            <div class="stat-label">Total Purchased</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($suppliers->sum('total_purchases'), 0) }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th class="text-right">Total Purchases</th>
                    <th class="text-right">Amount Paid</th>
                    <th class="text-right">Outstanding</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td class="fw-semibold">
                        <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" class="text-primary">
                            {{ $supplier->supplier_name }}
                        </a>
                    </td>
                    <td>{{ $supplier->contact_person ?? '—' }}</td>
                    <td>{{ $supplier->phone ?? '—' }}</td>
                    <td class="text-right">Rs. {{ number_format($supplier->total_purchases, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($supplier->total_paid, 0) }}</td>
                    <td class="text-right fw-semibold {{ $supplier->outstanding > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format($supplier->outstanding, 0) }}
                    </td>
                    <td>
                        @if($supplier->outstanding <= 0)
                            <span class="badge badge-success">Cleared</span>
                        @elseif($supplier->outstanding < $supplier->total_purchases * 0.5)
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Owing</span>
                        @endif
                    </td>
                    <td>
                        @if($supplier->outstanding > 0)
                            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">Pay Now</a>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">🏭</div>
                        <div class="empty-state-text">No supplier data found</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection