@extends('layouts.app')
@section('title', 'Inventory Report')
@section('page_title', 'Inventory Report')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🗃️ Inventory Report</div>
        <div class="page-subtitle">Current stock valuation and status</div>
    </div>
    <button onclick="window.print()" class="btn btn-secondary no-print">🖨️ Print</button>
</div>

<form method="GET" action="{{ route('reports.inventory') }}" class="no-print">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" style="min-width:160px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Stock Status</label>
            <select name="stock_status" class="form-select" style="min-width:140px;">
                <option value="">All Products</option>
                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low"      {{ request('stock_status') === 'low'      ? 'selected' : '' }}>Low Stock</option>
                <option value="out"      {{ request('stock_status') === 'out'      ? 'selected' : '' }}>Out of Stock</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Generate</button>
                <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">📦</div>
        <div>
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ $summary['total_products'] }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">🏪</div>
        <div>
            <div class="stat-label">Total Stock Units</div>
            <div class="stat-value">{{ number_format($summary['total_stock_qty']) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-cyan">💵</div>
        <div>
            <div class="stat-label">Total Stock Value</div>
            <div class="stat-value" style="font-size:18px;">Rs. {{ number_format($summary['total_value'], 0) }}</div>
            <div class="stat-sub">At purchase price</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">⚠️</div>
        <div>
            <div class="stat-label">Low / Out of Stock</div>
            <div class="stat-value">{{ $summary['low_stock_count'] + $summary['out_of_stock'] }}</div>
            <div class="stat-sub">{{ $summary['out_of_stock'] }} completely out</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Stock Valuation</span>
        <span class="badge badge-primary">{{ $summary['total_products'] }} products</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-right">Stock Qty</th>
                    <th class="text-right">Reorder Level</th>
                    <th class="text-right">Purchase Price</th>
                    <th class="text-right">Selling Price</th>
                    <th class="text-right">Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    if ($product->stock_quantity <= 0)
                        { $badge = 'badge-danger'; $statusLabel = 'Out of Stock'; }
                    elseif ($product->stock_quantity <= $product->reorder_level)
                        { $badge = 'badge-warning'; $statusLabel = 'Low Stock'; }
                    else
                        { $badge = 'badge-success'; $statusLabel = 'In Stock'; }
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td><code style="font-size:11px;background:var(--gray-100);padding:2px 5px;border-radius:3px;">{{ $product->sku }}</code></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td class="text-right">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                    <td class="text-right text-muted">{{ $product->reorder_level }}</td>
                    <td class="text-right">Rs. {{ number_format($product->purchase_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($product->selling_price, 2) }}</td>
                    <td class="text-right fw-semibold">Rs. {{ number_format($product->stock_quantity * $product->purchase_price, 0) }}</td>
                    <td><span class="badge {{ $badge }}">{{ $statusLabel }}</span></td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">🗃️</div>
                        <div class="empty-state-text">No products found</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
            @if($products->count())
            <tfoot>
                <tr style="background:var(--gray-50);font-weight:700;">
                    <td colspan="7" class="fw-bold">Total Stock Value</td>
                    <td class="text-right">Rs. {{ number_format($summary['total_value'], 0) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
