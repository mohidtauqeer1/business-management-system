@extends('layouts.app')
@section('title', 'Inventory')
@section('page_title', 'Stock Overview')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Stock Overview</div>
        <div class="page-subtitle">Monitor current inventory levels</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('inventory.adjustment.create') }}" class="btn btn-warning">⚙️ Adjust Stock</a>
        <a href="{{ route('inventory.low-stock') }}" class="btn btn-secondary">⚠️ Low Stock</a>
    </div>
</div>

<form method="GET" action="{{ route('inventory.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" style="min-width:200px;"
                   placeholder="Product name or SKU…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" style="min-width:150px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Stock Status</label>
            <select name="stock_status" class="form-select" style="min-width:140px;">
                <option value="">All Stock</option>
                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low"      {{ request('stock_status') === 'low'      ? 'selected' : '' }}>Low Stock</option>
                <option value="out"      {{ request('stock_status') === 'out'      ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    if ($product->stock_quantity <= 0) {
                        $status = 'Out of Stock'; $badge = 'badge-danger';
                    } elseif ($product->stock_quantity <= $product->reorder_level) {
                        $status = 'Low Stock'; $badge = 'badge-warning';
                    } else {
                        $status = 'In Stock'; $badge = 'badge-success';
                    }
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td><code style="font-size:11px;background:var(--gray-100);padding:2px 5px;border-radius:3px;">{{ $product->sku }}</code></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $badge }}">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                    </td>
                    <td class="text-muted">{{ $product->reorder_level }} {{ $product->unit }}</td>
                    <td class="fw-semibold">Rs. {{ number_format($product->stock_quantity * $product->purchase_price, 0) }}</td>
                    <td><span class="badge {{ $badge }}">{{ $status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-icon">🏪</div>
                        <div class="empty-state-text">No products found</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="pagination-wrapper">{{ $products->links() }}</div>
    @endif
</div>

@endsection