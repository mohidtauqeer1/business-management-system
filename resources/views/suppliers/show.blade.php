@extends('layouts.app')
@section('title', $supplier->name)
@section('page_title', 'Supplier Details')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">{{ $supplier->name }}</div>
        <div class="page-subtitle">Supplier profile & purchase history</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;max-width:1000px;">
    <div class="card">
        <div class="card-header"><span class="card-title">Contact Info</span></div>
        <div class="card-body">
            <table class="table">
                <tr><td class="text-muted" style="width:120px;">Name</td><td class="fw-semibold">{{ $supplier->name }}</td></tr>
                <tr><td class="text-muted">Contact</td><td>{{ $supplier->contact_person ?? '—' }}</td></tr>
                <tr><td class="text-muted">Phone</td><td>{{ $supplier->phone ?? '—' }}</td></tr>
                <tr><td class="text-muted">Email</td><td>{{ $supplier->email ?? '—' }}</td></tr>
                <tr><td class="text-muted">Address</td><td>{{ $supplier->address ?? '—' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Purchase History</span>
            <span class="badge badge-primary">{{ $supplier->purchases->count() }} orders</span>
        </div>
        @if($supplier->purchases->count())
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>Invoice</th><th>Date</th><th>Total</th><th>Paid</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($supplier->purchases as $purchase)
                    <tr>
                        <td><a href="{{ route('purchases.show', $purchase) }}" class="text-primary fw-semibold">{{ $purchase->invoice_number }}</a></td>
                        <td class="text-muted">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</td>
                        <td>Rs. {{ number_format($purchase->total_amount, 0) }}</td>
                        <td>Rs. {{ number_format($purchase->paid_amount, 0) }}</td>
                        <td>
                            @if($purchase->payment_status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($purchase->payment_status === 'partial')
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
        <div class="empty-state"><div class="empty-state-text">No purchases yet</div></div>
        @endif
    </div>
</div>

@endsection