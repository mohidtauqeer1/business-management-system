<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
</head>
<body>

<h1>Products</h1>

@if(session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif

@if(session('error'))
    <p style="color: red;">
        {{ session('error') }}
    </p>
@endif

<a href="{{ route('products.create') }}">
    Add Product
</a>

<hr>

<form method="GET" action="{{ route('products.index') }}">

    <input
        type="text"
        name="search"
        placeholder="Search name or SKU"
        value="{{ request('search') }}"
    >

    <select name="category_id">
        <option value="">All Categories</option>

        @foreach($categories as $category)
            <option
                value="{{ $category->id }}"
                {{ request('category_id') == $category->id ? 'selected' : '' }}
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="status">
        <option value="">All Status</option>

        <option
            value="active"
            {{ request('status') === 'active' ? 'selected' : '' }}
        >
            Active
        </option>

        <option
            value="discontinued"
            {{ request('status') === 'discontinued' ? 'selected' : '' }}
        >
            Discontinued
        </option>
    </select>

    <button type="submit">
        Filter
    </button>

    <a href="{{ route('products.index') }}">
        Clear
    </a>

</form>

<hr>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
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
                <td>{{ $product->id }}</td>

                <td>{{ $product->name }}</td>

                <td>{{ $product->sku }}</td>

                <td>
                    {{ $product->category?->name ?? 'Uncategorized' }}
                </td>

                <td>
                    {{ number_format($product->purchase_price, 2) }}
                </td>

                <td>
                    {{ number_format($product->selling_price, 2) }}
                </td>

                <td>
                    {{ $product->stock_quantity }}
                    {{ $product->unit }}
                </td>

                <td>
                    {{ ucfirst($product->status) }}
                </td>

                <td>
                    <a href="{{ route('products.show', $product) }}">
                        View
                    </a>

                    |

                    <a href="{{ route('products.edit', $product) }}">
                        Edit
                    </a>

                    |

                    <form
                        method="POST"
                        action="{{ route('products.destroy', $product) }}"
                        style="display:inline;"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            onclick="return confirm('Delete this product?')"
                        >
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="9">
                    No products found.
                </td>
            </tr>

        @endforelse

    </tbody>
</table>

{{ $products->links() }}

</body>
</html>