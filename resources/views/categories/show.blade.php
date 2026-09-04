<!DOCTYPE html>
<html>
<head>
    <title>Category Details</title>
</head>
<body>

<h1>{{ $category->name }}</h1>

<p>
    <strong>ID:</strong>
    {{ $category->id }}
</p>

<p>
    <strong>Description:</strong>
    {{ $category->description ?? '-' }}
</p>

<p>
    <strong>Parent:</strong>
    {{ $category->parent?->name ?? 'Root Category' }}
</p>

<h2>Child Categories</h2>

<ul>

@forelse($category->children as $child)

    <li>{{ $child->name }}</li>

@empty

    <li>No child categories.</li>

@endforelse

</ul>

<h2>Products</h2>

<ul>

@forelse($category->products as $product)

    <li>
        {{ $product->name }}
        — Stock: {{ $product->stock_quantity }}
    </li>

@empty

    <li>No products in this category.</li>

@endforelse

</ul>

<br>

<a href="{{ route('categories.edit', $category) }}">
    Edit
</a>

<br><br>

<a href="{{ route('categories.index') }}">
    Back to Categories
</a>

</body>
</html>