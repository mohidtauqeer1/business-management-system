<!DOCTYPE html>
<html>
<head>
    <title>{{ $product->name }}</title>
</head>
<body>

<h1>{{ $product->name }}</h1>

<p>
    <strong>SKU:</strong>
    {{ $product->sku }}
</p>

<p>
    <strong>Category:</strong>
    {{ $product->category?->name ?? 'Uncategorized' }}
</p>

<p>
    <strong>Purchase Price:</strong>
    {{ number_format($product->purchase_price, 2) }}
</p>

<p>
    <strong>Selling Price:</strong>
    {{ number_format($product->selling_price, 2) }}
</p>

<p>
    <strong>Stock:</strong>
    {{ $product->stock_quantity }} {{ $product->unit }}
</p>

<p>
    <strong>Reorder Level:</strong>
    {{ $product->reorder_level }}
</p>

<p>
    <strong>Status:</strong>
    {{ ucfirst($product->status) }}
</p>

<a href="{{ route('products.edit', $product) }}">
    Edit
</a>

|

<a href="{{ route('products.index') }}">
    Back
</a>

</body>
</html>