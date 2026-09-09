@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

{{-- KPI Row 1 --}}
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

{{-- KPI Row 2 --}}
<div class="stats-grid" style="margin-top:-8px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">📤</div>
        <div>
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($todaySalesTotal, 0) }}</div>
            <div class="stat-sub">{{ $todaySalesCount }} transactions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📥</div>
        <div>
            <div class="stat-label">Today's Purchases</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($todayPurchTotal, 0) }}</div>
            <div class="stat-sub">{{ $todayPurchCount }} orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">💸</div>
        <div>
            <div class="stat-label">Receivables</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($totalReceivables, 0) }}</div>
            <div class="stat-sub">{{ $unpaidSalesCount }} unpaid sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">💳</div>
        <div>
            <div class="stat-label">Payables</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($totalPayables, 0) }}</div>
            <div class="stat-sub">{{ $unpaidPurchasesCount }} unpaid purchases</div>
        </div>
    </div>
</div>

{{-- Chart Row: Sales Trend + Payment Breakdown --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px;">

    {{-- Sales vs Purchases 30-day trend --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📈 Revenue Trend — Last 30 Days</span>
        </div>
        <div class="card-body">
            <canvas id="trendChart" height="120"></canvas>
        </div>
    </div>

    {{-- Payment Status Doughnut --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🍩 Payment Status</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:center;">
            <canvas id="paymentChart" height="180"></canvas>
        </div>
    </div>

</div>

{{-- Chart Row: Top Products + Expenses --}}
<div style="display:grid;grid-template-columns:3fr 2fr;gap:24px;margin-bottom:24px;">

    {{-- Top 10 products --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🏆 Top Products This Month</span>
        </div>
        <div class="card-body">
            @if($topProducts->isEmpty())
                <div class="empty-state" style="padding:20px;">
                    <div class="empty-state-icon">📊</div>
                    <div class="empty-state-text">No sales this month yet</div>
                </div>
            @else
            <canvas id="topProductsChart" height="140"></canvas>
            @endif
        </div>
    </div>

    {{-- This Month Summary + Expenses by Category --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 {{ now()->format('F Y') }} Summary</span>
        </div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--success-light);border-radius:8px;">
                    <span style="font-size:13px;font-weight:600;color:var(--success-dark);">💰 Total Sales</span>
                    <span style="font-weight:800;color:var(--success-dark);">Rs. {{ number_format($monthSalesTotal, 0) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--primary-light);border-radius:8px;">
                    <span style="font-size:13px;font-weight:600;color:var(--primary-dark);">🛒 Total Purchases</span>
                    <span style="font-weight:800;color:var(--primary-dark);">Rs. {{ number_format($monthPurchTotal, 0) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:var(--danger-light);border-radius:8px;">
                    <span style="font-size:13px;font-weight:600;color:var(--danger-dark);">💸 Expenses</span>
                    <span style="font-weight:800;color:var(--danger-dark);">Rs. {{ number_format($monthExpenses, 0) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:{{ $monthNetProfit >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};border-radius:8px;border:2px solid {{ $monthNetProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                    <span style="font-size:14px;font-weight:700;color:{{ $monthNetProfit >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        {{ $monthNetProfit >= 0 ? '📊' : '📉' }} Net Profit
                    </span>
                    <span style="font-weight:900;font-size:16px;color:{{ $monthNetProfit >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        {{ $monthNetProfit < 0 ? '-' : '' }}Rs. {{ number_format(abs($monthNetProfit), 0) }}
                    </span>
                </div>

                @if($expenseByCategory->isNotEmpty())
                <div style="margin-top:8px;border-top:1px solid var(--gray-100);padding-top:12px;">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gray-400);margin-bottom:8px;">Expenses by Category</div>
                    <canvas id="expenseChart" height="120"></canvas>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Credit Limit Warnings --}}
@if($nearLimitCustomers->isNotEmpty())
<div class="card" style="margin-bottom:24px;border-left:4px solid var(--warning);">
    <div class="card-header">
        <span class="card-title">⚠️ Customers Near Credit Limit</span>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr><th>Customer</th><th class="text-right">Outstanding</th><th class="text-right">Credit Limit</th><th>Usage</th></tr>
            </thead>
            <tbody>
                @foreach($nearLimitCustomers as $c)
                @php $pct = $c->credit_limit > 0 ? min(100, ($c->balance / $c->credit_limit) * 100) : 0; @endphp
                <tr>
                    <td class="fw-semibold">{{ $c->name }}</td>
                    <td class="text-right text-danger fw-bold">Rs. {{ number_format($c->balance, 0) }}</td>
                    <td class="text-right text-muted">Rs. {{ number_format($c->credit_limit, 0) }}</td>
                    <td style="min-width:140px;">
                        <div style="background:var(--gray-100);border-radius:999px;height:8px;overflow:hidden;">
                            <div style="width:{{ $pct }}%;height:100%;background:{{ $pct >= 100 ? 'var(--danger)' : 'var(--warning)' }};border-radius:999px;transition:width 0.3s;"></div>
                        </div>
                        <div style="font-size:11px;color:var(--gray-400);margin-top:2px;">{{ number_format($pct, 0) }}% used</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Bottom Grid: Recent Sales + Recent Purchases + Low Stock --}}
<div class="dashboard-grid">

    <div>
        {{-- Recent Sales --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <span class="card-title">📤 Recent Sales</span>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            @if($recentSales->isNotEmpty())
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}" class="text-primary fw-semibold">{{ $sale->invoice_number }}</a></td>
                            <td>{{ $sale->customer?->name ?? 'Walk-in' }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($sale->total_amount, 0) }}</td>
                            <td>
                                @if($sale->payment_status === 'paid') <span class="badge badge-success">Paid</span>
                                @elseif($sale->payment_status === 'partial') <span class="badge badge-warning">Partial</span>
                                @else <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state"><div class="empty-state-icon">📤</div><div class="empty-state-text">No sales yet</div></div>
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
                    <thead><tr><th>Invoice</th><th>Supplier</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach($recentPurchases as $purchase)
                        <tr>
                            <td><a href="{{ route('purchases.show', $purchase) }}" class="text-primary fw-semibold">{{ $purchase->invoice_number }}</a></td>
                            <td>{{ $purchase->supplier?->name ?? 'Unknown' }}</td>
                            <td class="fw-semibold">Rs. {{ number_format($purchase->total_amount, 0) }}</td>
                            <td>
                                @if($purchase->payment_status === 'paid') <span class="badge badge-success">Paid</span>
                                @elseif($purchase->payment_status === 'partial') <span class="badge badge-warning">Partial</span>
                                @else <span class="badge badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state"><div class="empty-state-icon">📥</div><div class="empty-state-text">No purchases yet</div></div>
            @endif
        </div>
    </div>

    {{-- Right Column: Low Stock --}}
    <div>
        @if($lowStockProducts->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <span class="card-title">⚠️ Low Stock Alert</span>
                <a href="{{ route('inventory.low-stock') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead><tr><th>Product</th><th>Stock</th><th>Min</th></tr></thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td style="font-weight:600;font-size:13px;">{{ $product->name }}</td>
                            <td><span class="badge {{ $product->stock_quantity <= 0 ? 'badge-danger' : 'badge-warning' }}">{{ $product->stock_quantity }} {{ $product->unit }}</span></td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#94a3b8';

// ── 1. Revenue Trend ──────────────────────────────────────────────────────────
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartDates),
            datasets: [
                {
                    label: 'Sales',
                    data: @json($chartSales),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Purchases',
                    data: @json($chartPurchases),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.06)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rs. ' + Number(ctx.raw).toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148,163,184,0.1)' },
                    ticks: { callback: v => 'Rs.' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v) }
                },
                x: { grid: { display: false } }
            }
        }
    });
}

// ── 2. Payment Doughnut ───────────────────────────────────────────────────────
const payCtx = document.getElementById('paymentChart');
if (payCtx) {
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Partial', 'Unpaid'],
            datasets: [{
                data: [
                    {{ $paymentBreakdown['paid'] }},
                    {{ $paymentBreakdown['partial'] }},
                    {{ $paymentBreakdown['unpaid'] }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// ── 3. Top Products Bar ───────────────────────────────────────────────────────
const prodCtx = document.getElementById('topProductsChart');
if (prodCtx) {
    const topData = @json($topProducts);
    new Chart(prodCtx, {
        type: 'bar',
        data: {
            labels: topData.map(p => p.name.length > 20 ? p.name.substring(0,20)+'…' : p.name),
            datasets: [{
                label: 'Revenue (Rs.)',
                data: topData.map(p => p.revenue),
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rs. ' + Number(ctx.raw).toLocaleString() + ' (' + topData[ctx.dataIndex].qty_sold + ' units)'
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148,163,184,0.1)' },
                    ticks: { callback: v => 'Rs.' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v) }
                },
                y: { grid: { display: false } }
            }
        }
    });
}

// ── 4. Expenses Doughnut ──────────────────────────────────────────────────────
const expCtx = document.getElementById('expenseChart');
if (expCtx) {
    const expData = @json($expenseByCategory);
    const labels  = Object.keys(expData);
    const values  = Object.values(expData);
    const colors  = ['#ef4444','#f59e0b','#3b82f6','#10b981','#8b5cf6','#ec4899','#06b6d4','#84cc16','#f97316','#64748b'];

    new Chart(expCtx, {
        type: 'doughnut',
        data: {
            labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                legend: { position: 'right', labels: { font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rs. ' + Number(ctx.raw).toLocaleString()
                    }
                }
            }
        }
    });
}
</script>
@endpush