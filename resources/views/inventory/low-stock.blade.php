<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Products</title>
</head>
<body>

    <h1>Business Management System</h1>

    <h2>Low Stock Products</h2>

    <p>
        Welcome, {{ auth()->user()->name }}
        |
        Role: {{ auth()->user()->role }}
    </p>

    <a href="{{ route('inventory.index') }}">Inventory</a>
    |
    <a href="{{ route('inventory.adjustment.create') }}">Adjust Stock</a>
    |
    <a href="{{ route('inventory.movements') }}">Stock History</a>

    <hr>

    <table border="1" cellpadding="8">

        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Unit</th>
            </tr>
        </thead>

        <tbody>

            @forelse($products as $product)

                <tr>
                    <td>{{ $product->name }}</td>

                    <td>{{ $product->sku }}</td>

                    <td>
                        {{ $product->category?->name ?? 'Uncategorized' }}
                    </td>

                    <td>{{ $product->stock_quantity }}</td>

                    <td>{{ $product->reorder_level }}</td>

                    <td>{{ $product->unit }}</td>
                </tr>

            @empty

                <tr>
                    <td colspan="6">
                        No low-stock products.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <br>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>