@extends('layouts.app')
@section('title', 'Stock Movements')
@section('page_title', 'Stock Movements')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Stock Movements</div>
        <div class="page-subtitle">Complete history of all inventory changes</div>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">← Inventory</a>
</div>

<form method="GET" action="{{ route('inventory.movements') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" style="min-width:180px;">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" style="min-width:140px;">
                <option value="">All Types</option>
                <option value="in"  {{ request('type') === 'in'  ? 'selected' : '' }}>Stock In (+)</option>
                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stock Out (−)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="form-group">
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('inventory.movements') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th class="text-right">Qty Change</th>
                    <th class="text-right">Stock After</th>
                    <th>Reference</th>
                    <th>By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td class="text-muted" style="white-space:nowrap;">
                        {{ $movement->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="fw-semibold">{{ $movement->product?->name ?? '—' }}</td>
                    <td>
                        @if($movement->type === 'in')
                            <span class="badge badge-success">▲ In</span>
                        @else
                            <span class="badge badge-danger">▼ Out</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-gray">{{ ucfirst(str_replace('_', ' ', $movement->reason)) }}</span>
                    </td>
                    <td class="text-right fw-semibold {{ $movement->type === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $movement->type === 'in' ? '+' : '−' }}{{ $movement->quantity }}
                    </td>
                    <td class="text-right">{{ $movement->stock_after ?? '—' }}</td>
                    <td class="text-muted text-sm">{{ $movement->referenceable_type ? class_basename($movement->referenceable_type) . ' #' . $movement->referenceable_id : '—' }}</td>
                    <td>{{ $movement->user?->name ?? '—' }}</td>
                    <td class="text-muted text-sm">{{ $movement->notes ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">🔄</div>
                        <div class="empty-state-text">No stock movements found</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
    <div class="pagination-wrapper">{{ $movements->links() }}</div>
    @endif
</div>

@endsection