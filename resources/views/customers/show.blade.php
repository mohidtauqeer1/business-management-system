@extends('layouts.app')
@section('title', $customer->name)
@section('page_title', 'Customer Details')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">{{ $customer->name }}</div>
        <div class="page-subtitle">Customer profile & sales history</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;max-width:1000px;">
    <div class="card">
        <div class="card-header"><span class="card-title">Contact Info</span></div>
        <div class="card-body">
            <table class="table">
                <tr><td class="text-muted" style="width:120px;">Name</td><td class="fw-semibold">{{ $customer->name }}</td></tr>
                <tr><td class="text-muted">Phone</td><td>{{ $customer->phone ?? '—' }}</td></tr>
                <tr><td class="text-muted">Email</td><td>{{ $customer->email ?? '—' }}</td></tr>
                <tr><td class="text-muted">Address</td><td>{{ $customer->address ?? '—' }}</td></tr>
                <tr>
                    <td class="text-muted">Balance</td>
                    <td>
                        @if($customer->credit_balance > 0)
                            <span class="badge badge-warning">Rs. {{ number_format($customer->credit_balance, 2) }}</span>
                        @else
                            <span class="badge badge-success">Cleared</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Sales History</span>
            <span class="badge badge-primary">{{ $customer->sales->count() }} orders</span>
        </div>
        @if($customer->sales->count())
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>Invoice</th><th>Date</th><th>Total</th><th>Paid</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($customer->sales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}" class="text-primary fw-semibold">{{ $sale->invoice_number }}</a></td>
                        <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                        <td>Rs. {{ number_format($sale->total_amount, 0) }}</td>
                        <td>Rs. {{ number_format($sale->paid_amount, 0) }}</td>
                        <td>
                            @if($sale->payment_status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="badge badge-warning">Partial</span>
                            @else
                                <span class="badge badge-danger">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state"><div class="empty-state-text">No sales yet</div></div>
        @endif
    </div>
</div>

@endsection