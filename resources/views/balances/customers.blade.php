@extends('layouts.app')
@section('title', 'Customer Balances')
@section('page_title', 'Customer Balances')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Customer Balances</div>
        <div class="page-subtitle">Outstanding receivable amounts from customers</div>
    </div>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">+ Record Payment</a>
</div>

{{-- Summary Cards --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">👤</div>
        <div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $customers->count() }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">💸</div>
        <div>
            <div class="stat-label">Total Receivable</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($customers->sum('outstanding'), 0) }}
            </div>
            <div class="stat-sub">Across {{ $customers->where('outstanding', '>', 0)->count() }} customers</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📤</div>
        <div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($customers->sum('total_sales'), 0) }}
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div>
            <div class="stat-label">Total Received</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($customers->sum('total_received'), 0) }}
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th class="text-right">Total Sales</th>
                    <th class="text-right">Amount Received</th>
                    <th class="text-right">Outstanding</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="fw-semibold">
                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="text-primary">
                            {{ $customer->customer_name }}
                        </a>
                    </td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>{{ $customer->email ?? '—' }}</td>
                    <td class="text-right">Rs. {{ number_format($customer->total_sales, 0) }}</td>
                    <td class="text-right text-success">Rs. {{ number_format($customer->total_received, 0) }}</td>
                    <td class="text-right fw-semibold {{ $customer->outstanding > 0 ? 'text-danger' : 'text-success' }}">
                        Rs. {{ number_format($customer->outstanding, 0) }}
                    </td>
                    <td>
                        @if($customer->outstanding <= 0)
                            <span class="badge badge-success">Cleared</span>
                        @elseif($customer->outstanding < $customer->total_sales * 0.5)
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Owing</span>
                        @endif
                    </td>
                    <td>
                        @if($customer->outstanding > 0)
                            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm">Receive</a>
                        @else
                            <span class="text-muted text-sm">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <div class="empty-state-text">No customer data found</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
