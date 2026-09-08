@extends('layouts.app')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product')
@section('breadcrumb')
    <a href="{{ route('products.index') }}">Products</a> › Edit
@endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Edit: {{ $product->name }}</div>
        <div class="page-subtitle">SKU: {{ $product->sku }}</div>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">← Back to Products</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Product Information</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">SKU *</label>
                    <input type="text" name="sku" class="form-control {{ $errors->has('sku') ? 'is-invalid' : '' }}"
                           value="{{ old('sku', $product->sku) }}" required>
                    @error('sku')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Unit *</label>
                    <input type="text" name="unit" class="form-control"
                           value="{{ old('unit', $product->unit) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Purchase Price (Rs.) *</label>
                    <input type="number" name="purchase_price" class="form-control"
                           value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Selling Price (Rs.) *</label>
                    <input type="number" name="selling_price" class="form-control"
                           value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control"
                           value="{{ old('stock_quantity', $product->stock_quantity) }}" step="0.01" min="0" required>
                    <div class="form-hint">Use Stock Adjustment for inventory changes</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Level *</label>
                    <input type="number" name="reorder_level" class="form-control"
                           value="{{ old('reorder_level', $product->reorder_level) }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select">
                    <option value="active"       {{ old('status', $product->status) === 'active'       ? 'selected' : '' }}>Active</option>
                    <option value="discontinued" {{ old('status', $product->status) === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>

            <div class="divider"></div>

            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection