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
        <div class="card-header"><span class="card-title">Profit & Loss Summary</span></div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <tr>
                    <td style="padding:14px 20px;">Total Revenue</td>
                    <td class="text-right fw-semibold" style="padding:14px 20px;">
                        Rs. {{ number_format($summary['totalRevenue'], 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;" class="text-danger">Less: Discounts</td>
                    <td class="text-right text-danger" style="padding:14px 20px;">
                        -Rs. {{ number_format($summary['totalDiscounts'], 0) }}
                    </td>
                </tr>
                <tr style="background:var(--gray-50);">
                    <td style="padding:14px 20px;" class="fw-semibold">Net Revenue</td>
                    <td class="text-right fw-semibold" style="padding:14px 20px;">
                        Rs. {{ number_format($summary['netRevenue'], 0) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;" class="text-danger">Less: COGS</td>
                    <td class="text-right text-danger" style="padding:14px 20px;">
                        -Rs. {{ number_format($summary['cogs'], 0) }}
                    </td>
                </tr>
                <tr style="background:{{ $summary['grossProfit'] >= 0 ? 'var(--success-light)' : 'var(--danger-light)' }};">
                    <td style="padding:16px 20px;font-size:16px;font-weight:800;">
                        {{ $summary['grossProfit'] >= 0 ? '✅ Gross Profit' : '❌ Gross Loss' }}
                    </td>
                    <td class="text-right" style="padding:16px 20px;font-size:16px;font-weight:800;color:{{ $summary['grossProfit'] >= 0 ? 'var(--success-dark)' : 'var(--danger-dark)' }};">
                        Rs. {{ number_format(abs($summary['grossProfit']), 0) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Key Metrics</span></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:16px;">
                <div style="text-align:center;padding:20px;background:var(--gray-50);border-radius:8px;">
                    <div class="stat-label">Gross Margin</div>
                    <div style="font-size:42px;font-weight:800;color:{{ $summary['grossMargin'] >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                        {{ number_format($summary['grossMargin'], 1) }}%
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="text-align:center;padding:12px;background:var(--primary-light);border-radius:8px;">
                        <div class="stat-label">Total Invoices</div>
                        <div style="font-size:24px;font-weight:800;color:var(--primary-dark);">{{ $sales->count() }}</div>
                    </div>
                    <div style="text-align:center;padding:12px;background:var(--success-light);border-radius:8px;">
                        <div class="stat-label">Avg Sale Value</div>
                        <div style="font-size:20px;font-weight:800;color:var(--success-dark);">
                            Rs. {{ $sales->count() ? number_format($summary['totalRevenue'] / $sales->count(), 0) : '0' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

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
