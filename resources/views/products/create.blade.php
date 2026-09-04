<!DOCTYPE html>
<html>
<head>
    <title>Create Product</title>
</head>
<body>

<h1>Create Product</h1>

@if($errors->any())
    <ul style="color:red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('products.store') }}">

    @csrf

    <div>
        <label>Product Name</label>
        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>SKU</label>
        <input
            type="text"
            name="sku"
            value="{{ old('sku') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Category</label>

        <select name="category_id">
            <option value="">Select Category</option>

            @foreach($categories as $category)
                <option
                    value="{{ $category->id }}"
                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <br>

    <div>
        <label>Purchase Price</label>
        <input
            type="number"
            step="0.01"
            name="purchase_price"
            value="{{ old('purchase_price', 0) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Selling Price</label>
        <input
            type="number"
            step="0.01"
            name="selling_price"
            value="{{ old('selling_price', 0) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Opening Stock</label>
        <input
            type="number"
            step="0.01"
            name="stock_quantity"
            value="{{ old('stock_quantity', 0) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Unit</label>

        <select name="unit">
            <option value="pcs">Pieces</option>
            <option value="kg">KG</option>
            <option value="box">Box</option>
            <option value="liter">Liter</option>
            <option value="meter">Meter</option>
        </select>
    </div>

    <br>

    <div>
        <label>Reorder Level</label>
        <input
            type="number"
            step="0.01"
            name="reorder_level"
            value="{{ old('reorder_level', 0) }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Status</label>

        <select name="status">
            <option value="active">Active</option>
            <option value="discontinued">Discontinued</option>
        </select>
    </div>

    <br>

    <button type="submit">
        Create Product
    </button>

    <a href="{{ route('products.index') }}">
        Cancel
    </a>

</form>

</body>
</html>