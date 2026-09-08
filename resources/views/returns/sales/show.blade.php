@extends('layouts.app')
@section('title', 'Sale Return — ' . $saleReturn->return_number)
@section('page_title', 'Sale Return Details')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">↩️ {{ $saleReturn->return_number }}</div>
        <div class="page-subtitle">
            {{ \Carbon\Carbon::parse($saleReturn->return_date)->format('d M Y') }}
            &nbsp;·&nbsp; Processed by {{ $saleReturn->user?->name }}
        </div>
    </div>
    <div class="d-flex gap-8">
        <a href="{{ route('sales.show', $saleReturn->sale) }}" class="btn btn-secondary">View Original Invoice</a>
        <a href="{{ route('returns.sales.index') }}" class="btn btn-secondary">All Returns</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">

    {{-- Items Returned --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Returned Items</span>
            <span class="badge badge-danger">Stock Restored</span>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-center">Return Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Subtotal</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($saleReturn->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                        <td class="text-muted">{{ $item->reason ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:var(--gray-50);">
                        <td colspan="4" class="fw-bold">Total Return Value</td>
                        <td class="text-right fw-bold">Rs. {{ number_format($saleReturn->total_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Summary --}}
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><span class="card-title">Return Summary</span></div>
            <div class="card-body">
                <table class="table" style="margin:0;">
                    <tr>
                        <td class="text-muted">Return #</td>
                        <td class="fw-semibold text-right">{{ $saleReturn->return_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Original Invoice</td>
                        <td class="text-right">
                            <a href="{{ route('sales.show', $saleReturn->sale) }}" class="text-primary">
                                {{ $saleReturn->sale->invoice_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Customer</td>
                        <td class="fw-semibold text-right">{{ $saleReturn->sale->customer?->name ?? 'Walk-in' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Return Date</td>
                        <td class="text-right">{{ \Carbon\Carbon::parse($saleReturn->return_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Return Total</td>
                        <td class="text-right fw-semibold">Rs. {{ number_format($saleReturn->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Refund Amount</td>
                        <td class="text-right fw-semibold text-success">Rs. {{ number_format($saleReturn->refund_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Refund Method</td>
                        <td class="text-right">{{ ucfirst($saleReturn->refund_method) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Refund Status</td>
                        <td class="text-right">
                            @if($saleReturn->refund_status === 'refunded')
                                <span class="badge badge-success">Refunded</span>
                            @elseif($saleReturn->refund_status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-gray">No Refund</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($saleReturn->reason)
        <div class="card">
            <div class="card-header"><span class="card-title">Reason</span></div>
            <div class="card-body">
                <p style="color:var(--gray-600);line-height:1.6;">{{ $saleReturn->reason }}</p>
            </div>
        </div>
        @endif
    </div>

</div>

@endsection
