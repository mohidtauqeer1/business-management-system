@extends('layouts.app')
@section('title', 'Profit Report')
@section('page_title', 'Profit & Loss Report')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">💵 Profit Report</div>
        <div class="page-subtitle">{{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}</div>
    </div>
    <button onclick="window.print()" class="btn btn-secondary no-print">🖨️ Print</button>
</div>

<form method="GET" action="{{ route('reports.profit') }}" class="no-print">
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
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Generate</button>
                <a href="{{ route('reports.profit') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

{{-- P&L Summary --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;max-width:900px;">

    <div class="card">
        <div class="card-header"><span class="card-title">Profit & Loss Statement</span></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <tr>
                    <td style="padding:14px 20px;">Total Revenue</td>
                    <td class="text-right fw-semibold" style="padding:14px 20px;">Rs. {{ number_format($summary['totalRevenue'], 0) }}</td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;" class="text-danger">Less: Discounts</td>
                    <td class="text-right text-danger" style="padding:14px 20px;">-Rs. {{ number_format($summary['totalDiscounts'], 0) }}</td>
                </tr>
                <tr style="background:var(--gray-50);">
                    <td style="padding:14px 20px;" class="fw-semibold">Net Revenue</td>
                    <td class="text-right fw-semibold" style="padding:14px 20px;">Rs. {{ number_format($summary['netRevenue'], 0) }}</td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;" class="text-danger">Less: COGS</td>
                    <td class="text-right text-danger" style="padding:14px 20px;">-Rs. {{ number_format($summary['cogs'], 0) }}</td>
                </tr>
                <tr style="background:{{ $summary['grossProfit'] >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};">
                    <td style="padding:14px 20px;font-weight:700;">{{ $summary['grossProfit'] >= 0 ? '✅' : '❌' }} Gross Profit</td>
                    <td class="text-right fw-bold" style="padding:14px 20px;color:{{ $summary['grossProfit'] >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        Rs. {{ number_format(abs($summary['grossProfit']), 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;" class="text-danger">Less: Operating Expenses</td>
                    <td class="text-right text-danger" style="padding:14px 20px;">-Rs. {{ number_format($summary['totalExpenses'], 0) }}</td>
                </tr>
                <tr style="background:{{ $summary['netProfit'] >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};border-top:2px solid {{ $summary['netProfit'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                    <td style="padding:18px 20px;font-size:16px;font-weight:800;">
                        {{ $summary['netProfit'] >= 0 ? '📊 Net Profit' : '📉 Net Loss' }}
                    </td>
                    <td class="text-right" style="padding:18px 20px;font-size:16px;font-weight:900;color:{{ $summary['netProfit'] >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        {{ $summary['netProfit'] < 0 ? '-' : '' }}Rs. {{ number_format(abs($summary['netProfit']), 0) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Key Metrics</span></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="text-align:center;padding:16px;background:var(--gray-50);border-radius:8px;">
                    <div class="stat-label">Gross Margin</div>
                    <div style="font-size:38px;font-weight:800;color:{{ $summary['grossMargin'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ number_format($summary['grossMargin'], 1) }}%
                    </div>
                </div>
                <div style="text-align:center;padding:16px;background:{{ $summary['netMargin'] >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};border-radius:8px;">
                    <div class="stat-label">Net Margin</div>
                    <div style="font-size:38px;font-weight:800;color:{{ $summary['netMargin'] >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        {{ number_format($summary['netMargin'], 1) }}%
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="text-align:center;padding:10px;background:var(--primary-light);border-radius:8px;">
                        <div class="stat-label">Invoices</div>
                        <div style="font-size:22px;font-weight:800;color:var(--primary-dark);">{{ $sales->count() }}</div>
                    </div>
                    <div style="text-align:center;padding:10px;background:var(--warning-light);border-radius:8px;">
                        <div class="stat-label">Expenses</div>
                        <div style="font-size:22px;font-weight:800;color:var(--warning-dark);">{{ $expensesInPeriod->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Expenses Breakdown --}}
@if($expensesInPeriod->isNotEmpty())
<div class="card" style="margin-bottom:24px;max-width:900px;">
    <div class="card-header">
        <span class="card-title">💸 Operating Expenses Breakdown</span>
        <span class="fw-bold text-danger">Rs. {{ number_format($summary['totalExpenses'], 0) }}</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead><tr><th>Category</th><th class="text-right">Amount</th><th>% of Total</th></tr></thead>
            <tbody>
                @foreach($expenseByCategory->sortDesc() as $cat => $amount)
                @php $pct = $summary['totalExpenses'] > 0 ? ($amount / $summary['totalExpenses']) * 100 : 0; @endphp
                <tr>
                    <td class="fw-semibold">{{ \App\Models\Expense::categories()[$cat] ?? ucfirst($cat) }}</td>
                    <td class="text-right text-danger fw-semibold">Rs. {{ number_format($amount, 0) }}</td>
                    <td style="min-width:120px;">
                        <div style="background:var(--gray-100);border-radius:999px;height:6px;overflow:hidden;">
                            <div style="width:{{ $pct }}%;height:100%;background:var(--danger);border-radius:999px;"></div>
                        </div>
                        <span style="font-size:11px;color:var(--gray-400);">{{ number_format($pct, 1) }}%</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


{{-- Daily Breakdown --}}
@if($daily->count())
<div class="card">
    <div class="card-header">
        <span class="card-title">Daily Sales Breakdown</span>
        <span class="badge badge-gray">{{ $daily->count() }} days</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Invoices</th>
                    <th class="text-right">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily as $day)
                <tr>
                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($day['date'])->format('d M Y, D') }}</td>
                    <td class="text-right">{{ $day['count'] }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($day['revenue'], 0) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--gray-50);font-weight:700;">
                    <td>Total</td>
                    <td class="text-right">{{ $daily->sum('count') }}</td>
                    <td class="text-right">Rs. {{ number_format($daily->sum('revenue'), 0) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

@endsection
