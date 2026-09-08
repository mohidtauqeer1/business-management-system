@extends('layouts.app')

@section('title', 'Add Product')
@section('page_title', 'Add Product')
@section('breadcrumb')
    <a href="{{ route('products.index') }}">Products</a> › Add Product
@endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Add New Product</div>
        <div class="page-subtitle">Create a new product in your catalog</div>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">← Back to Products</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-header">
        <span class="card-title">Product Information</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Product Name *</label>
                    <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="e.g. HP ProBook 450" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="sku">SKU *</label>
                    <input type="text" id="sku" name="sku" class="form-control {{ $errors->has('sku') ? 'is-invalid' : '' }}"
                           value="{{ old('sku') }}" placeholder="e.g. HP-PB450-001" required>
                    @error('sku')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">No Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="unit">Unit *</label>
                    <input type="text" id="unit" name="unit" class="form-control"
                           value="{{ old('unit', 'pcs') }}" placeholder="pcs, kg, box…" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="purchase_price">Purchase Price (Rs.) *</label>
                    <input type="number" id="purchase_price" name="purchase_price" class="form-control {{ $errors->has('purchase_price') ? 'is-invalid' : '' }}"
                           value="{{ old('purchase_price') }}" step="0.01" min="0" required>
                    @error('purchase_price')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="selling_price">Selling Price (Rs.) *</label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control {{ $errors->has('selling_price') ? 'is-invalid' : '' }}"
                           value="{{ old('selling_price') }}" step="0.01" min="0" required>
                    @error('selling_price')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="stock_quantity">Opening Stock *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" class="form-control"
                           value="{{ old('stock_quantity', 0) }}" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reorder_level">Reorder Level *</label>
                    <input type="number" id="reorder_level" name="reorder_level" class="form-control"
                           value="{{ old('reorder_level', 5) }}" step="0.01" min="0" required>
                    <div class="form-hint">Alert when stock falls below this level</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select id="status" name="status" class="form-select">
                    <option value="active"       {{ old('status', 'active') === 'active'       ? 'selected' : '' }}>Active</option>
                    <option value="discontinued" {{ old('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>

            <div class="divider"></div>

            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection