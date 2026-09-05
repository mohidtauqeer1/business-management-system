<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
</head>
<body>

<h1>Inventory</h1>

<form method="GET" action="{{ route('inventory.index') }}">

    <input
        type="text"
        name="search"
        placeholder="Search product or SKU"
        value="{{ request('search') }}"
    >

    <select name="category_id">

        <option value="">
            All Categories
        </option>

        @foreach($categories as $category)

            <option
                value="{{ $category->id }}"
                {{ request('category_id') == $category->id ? 'selected' : '' }}
            >
                {{ $category->name }}
            </option>

        @endforeach

    </select>

    <select name="stock_status">

        <option value="">
            All Stock
        </option>

        <option
            value="in_stock"
            {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}
        >
            In Stock
        </option>

        <option
            value="low"
            {{ request('stock_status') === 'low' ? 'selected' : '' }}
        >
            Low Stock
        </option>

        <option
            value="out"
            {{ request('stock_status') === 'out' ? 'selected' : '' }}
        >
            Out of Stock
        </option>

    </select>

    <button type="submit">
        Filter
    </button>

    <a href="{{ route('inventory.index') }}">
        Clear
    </a>

</form>

<hr>

<table border="1" cellpadding="10" cellspacing="0" width="100%">

    <thead>

        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Reorder Level</th>
            <th>Stock Status</th>
        </tr>

    </thead>

    <tbody>

        @forelse($products as $product)

            @php
                if ($product->stock_quantity <= 0) {
                    $stockStatus = 'Out of Stock';
                } elseif (
                    $product->stock_quantity <= $product->reorder_level
                ) {
                    $stockStatus = 'Low Stock';
                } else {
                    $stockStatus = 'In Stock';
                }
            @endphp

            <tr>

                <td>
                    {{ $product->id }}
                </td>

                <td>
                    {{ $product->name }}
                </td>

                <td>
                    {{ $product->sku }}
                </td>

                <td>
                    {{ $product->category?->name ?? 'Uncategorized' }}
                </td>

                <td>
                    {{ $product->stock_quantity }}
                    {{ $product->unit }}
                </td>

                <td>
                    {{ $product->reorder_level }}
                    {{ $product->unit }}
                </td>

                <td>
                    {{ $stockStatus }}
                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7">
                    No products found.
                </td>

            </tr>

        @endforelse

    </tbody>

</table>

<br>

{{ $products->links() }}

</body>
</html>