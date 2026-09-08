@extends('layouts.app')
@section('title', 'Low Stock Products')
@section('page_title', 'Low Stock Alert')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">⚠️ Low Stock Alert</div>
        <div class="page-subtitle">Products at or below their reorder level</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">← Back to Inventory</a>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
    </div>
</div>

@if($products->count())
<div class="alert alert-warning">
    <span class="alert-icon">⚠️</span>
    <span><strong>{{ $products->count() }} product(s)</strong> need restocking immediately.</span>
</div>
@endif

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
                    <th>Shortage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td><code style="font-size:11px;background:var(--gray-100);padding:2px 5px;border-radius:3px;">{{ $product->sku }}</code></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>
                        @if($product->stock_quantity <= 0)
                            <span class="badge badge-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @else
                            <span class="badge badge-warning">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $product->reorder_level }} {{ $product->unit }}</td>
                    <td class="text-danger fw-semibold">
                        {{ max(0, $product->reorder_level - $product->stock_quantity) }} {{ $product->unit }}
                    </td>
                    <td>
                        @if($product->stock_quantity <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @else
                            <span class="badge badge-warning">Low Stock</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <div class="empty-state-icon">✅</div>
                        <div class="empty-state-text">All products are well-stocked!</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection