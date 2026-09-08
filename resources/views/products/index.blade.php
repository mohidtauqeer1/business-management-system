@extends('layouts.app')

@section('title', 'Products')
@section('page_title', 'Products')
@section('breadcrumb') Products @endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Products</div>
        <div class="page-subtitle">Manage your product catalog</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            + Add Product
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('products.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Name or SKU…" value="{{ request('search') }}" style="min-width:200px;">
        </div>
        <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" style="min-width:150px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" style="min-width:130px;">
                <option value="">All Status</option>
                <option value="active"       {{ request('status') === 'active'       ? 'selected' : '' }}>Active</option>
                <option value="discontinued" {{ request('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Purchase Price</th>
                    <th>Selling Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="text-muted">{{ $product->id }}</td>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td><code style="font-size:12px;background:var(--gray-100);padding:2px 6px;border-radius:4px;">{{ $product->sku }}</code></td>
                    <td>{{ $product->category?->name ?? '—' }}</td>
                    <td>Rs. {{ number_format($product->purchase_price, 2) }}</td>
                    <td class="fw-semibold">Rs. {{ number_format($product->selling_price, 2) }}</td>
                    <td>
                        @if($product->stock_quantity <= 0)
                            <span class="badge badge-danger">0 {{ $product->unit }}</span>
                        @elseif($product->stock_quantity <= $product->reorder_level)
                            <span class="badge badge-warning">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @else
                            <span class="badge badge-success">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-gray">Discontinued</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-8">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <div class="empty-state-text">No products found</div>
                            <div class="empty-state-sub">
                                <a href="{{ route('products.create') }}" class="text-primary">Add your first product</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="pagination-wrapper">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection