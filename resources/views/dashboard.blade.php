@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

{{-- KPI Stats Row --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📦</div>
        <div>
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-sub">{{ $activeProducts }} active</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">👤</div>
        <div>
            <div class="stat-label">Customers</div>
            <div class="stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="stat-sub">Registered</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">🏭</div>
        <div>
            <div class="stat-label">Suppliers</div>
            <div class="stat-value">{{ number_format($totalSuppliers) }}</div>
            <div class="stat-sub">Active</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-red">⚠️</div>
        <div>
            <div class="stat-label">Low Stock</div>
            <div class="stat-value">{{ number_format($lowStockCount) }}</div>
            <div class="stat-sub">Products need reorder</div>
        </div>
    </div>

</div>

{{-- Financial KPI Row --}}
<div class="stats-grid" style="margin-top:-8px;">

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">📤</div>
        <div>
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($todaySalesTotal, 0) }}
            </div>
            <div class="stat-sub">{{ $todaySalesCount }} transactions</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📥</div>
        <div>
            <div class="stat-label">Today's Purchases</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($todayPurchTotal, 0) }}
            </div>
            <div class="stat-sub">{{ $todayPurchCount }} orders</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">💸</div>
        <div>
            <div class="stat-label">Receivables</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($totalReceivables, 0) }}
            </div>
            <div class="stat-sub">{{ $unpaidSalesCount }} unpaid sales</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-red">💳</div>
        <div>
            <div class="stat-label">Payables</div>
            <div class="stat-value" style="font-size:20px;">
                Rs. {{ number_format($totalPayables, 0) }}
            </div>
            <div class="stat-sub">{{ $unpaidPurchasesCount }} unpaid purchases</div>
        </div>
    </div>

</div>

{{-- Main Grid --}}
<div class="dashboard-grid">

    {{-- Recent Sales --}}
    <div>
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">📤 Recent Sales</span>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>

            @if($recentSales->isNotEmpty())
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}"
                                   class="text-primary fw-semibold">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($sale->total_amount, 0) }}</td>
                            <td>
                                @if($sale->payment_status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($sale->payment_status === 'partial')
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📤</div>
                <div class="empty-state-text">No sales yet</div>
                <div class="empty-state-sub">Create your first sale to see it here</div>
            </div>
            @endif
        </div>

        {{-- Recent Purchases --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📥 Recent Purchases</span>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>

            @if($recentPurchases->isNotEmpty())
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPurchases as $purchase)
                        <tr>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}"
                                   class="text-primary fw-semibold">
                                    {{ $purchase->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $purchase->supplier?->name ?? 'Unknown' }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($purchase->total_amount, 0) }}</td>
                            <td>
                                @if($purchase->payment_status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($purchase->payment_status === 'partial')
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">📥</div>
                <div class="empty-state-text">No purchases yet</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div>

        {{-- This Month --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">📅 This Month</span>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--success-light);border-radius:8px;">
                        <span style="font-size:13px;font-weight:600;color:var(--success-dark);">💰 Total Sales</span>
                        <span style="font-weight:800;color:var(--success-dark);">Rs. {{ number_format($monthSalesTotal, 0) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--primary-light);border-radius:8px;">
                        <span style="font-size:13px;font-weight:600;color:var(--primary-dark);">🛒 Total Purchases</span>
                        <span style="font-weight:800;color:var(--primary-dark);">Rs. {{ number_format($monthPurchTotal, 0) }}</span>
                    </div>
                    @php $monthProfit = $monthSalesTotal - $monthPurchTotal; @endphp
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:{{ $monthProfit >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};border-radius:8px;">
                        <span style="font-size:13px;font-weight:600;color:{{ $monthProfit >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">📊 Gross Margin</span>
                        <span style="font-weight:800;color:{{ $monthProfit >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                            Rs. {{ number_format(abs($monthProfit), 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Low Stock Alert --}}
        @if($lowStockProducts->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚠️ Low Stock Alert</span>
                <a href="{{ route('inventory.low-stock') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td style="font-weight:600;font-size:13px;">{{ $product->name }}</td>
                            <td>
                                <span class="badge {{ $product->stock_quantity <= 0 ? 'badge-danger' : 'badge-warning' }}">
                                    {{ $product->stock_quantity }} {{ $product->unit }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $product->reorder_level }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state" style="padding:24px;">
                    <div class="empty-state-icon">✅</div>
                    <div class="empty-state-text">All products well-stocked</div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection