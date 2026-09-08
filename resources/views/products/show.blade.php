@extends('layouts.app')

@section('title', $product->name)
@section('page_title', 'Product Details')
@section('breadcrumb')
    <a href="{{ route('products.index') }}">Products</a> › {{ $product->name }}
@endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">{{ $product->name }}</div>
        <div class="page-subtitle">SKU: {{ $product->sku }}</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

<div class="stats-grid" style="max-width:900px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">💰</div>
        <div>
            <div class="stat-label">Purchase Price</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($product->purchase_price, 2) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">🏷️</div>
        <div>
            <div class="stat-label">Selling Price</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($product->selling_price, 2) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $product->stock_quantity <= $product->reorder_level ? 'stat-icon-red' : 'stat-icon-green' }}">📦</div>
        <div>
            <div class="stat-label">Current Stock</div>
            <div class="stat-value">{{ $product->stock_quantity }} {{ $product->unit }}</div>
            <div class="stat-sub">Reorder at {{ $product->reorder_level }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">📊</div>
        <div>
            <div class="stat-label">Status</div>
            <div class="stat-value" style="font-size:16px;">
                @if($product->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-gray">Discontinued</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card" style="max-width:900px;">
    <div class="card-header"><span class="card-title">Product Details</span></div>
    <div class="card-body">
        <table class="table" style="max-width:500px;">
            <tr><td class="text-muted" style="width:180px;">Category</td><td class="fw-semibold">{{ $product->category?->name ?? '—' }}</td></tr>
            <tr><td class="text-muted">Unit</td><td>{{ $product->unit }}</td></tr>
            <tr><td class="text-muted">Reorder Level</td><td>{{ $product->reorder_level }} {{ $product->unit }}</td></tr>
            <tr><td class="text-muted">Created</td><td>{{ $product->created_at->format('d M Y') }}</td></tr>
            <tr><td class="text-muted">Last Updated</td><td>{{ $product->updated_at->format('d M Y H:i') }}</td></tr>
        </table>
    </div>
</div>

@endsection