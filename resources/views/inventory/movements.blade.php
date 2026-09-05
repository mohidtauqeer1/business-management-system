<!DOCTYPE html>
<html>
<head>
    <title>Stock Movement History</title>
</head>
<body>

    <h1>Business Management System</h1>

    <h2>Stock Movement History</h2>

    <p>
        Welcome, {{ auth()->user()->name }}
        |
        Role: {{ auth()->user()->role }}
    </p>

    <a href="{{ route('inventory.index') }}">Inventory</a>
    |
    <a href="{{ route('inventory.adjustment.create') }}">Adjust Stock</a>
    |
    <a href="{{ route('inventory.low-stock') }}">Low Stock</a>

    <hr>

    <form method="GET" action="{{ route('inventory.movements') }}">

        <select name="product_id">
            <option value="">All Products</option>

            @foreach($products as $product)
                <option
                    value="{{ $product->id }}"
                    @selected(request('product_id') == $product->id)
                >
                    {{ $product->name }}
                </option>
            @endforeach
        </select>

        <select name="type">
            <option value="">All Types</option>

            <option value="purchase"
                @selected(request('type') === 'purchase')>
                Purchase
            </option>

            <option value="sale"
                @selected(request('type') === 'sale')>
                Sale
            </option>

            <option value="adjustment"
                @selected(request('type') === 'adjustment')>
                Adjustment
            </option>

            <option value="purchase_return"
                @selected(request('type') === 'purchase_return')>
                Purchase Return
            </option>

            <option value="sale_return"
                @selected(request('type') === 'sale_return')>
                Sale Return
            </option>
        </select>

        <button type="submit">Filter</button>

        <a href="{{ route('inventory.movements') }}">Reset</a>

    </form>

    <br>

    <table border="1" cellpadding="8">

        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Stock Before</th>
                <th>Stock After</th>
                <th>User</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>

            @forelse($movements as $movement)

                <tr>
                    <td>
                        {{ $movement->created_at->format('d M Y H:i') }}
                    </td>

                    <td>
                        {{ $movement->product->name }}
                    </td>

                    <td>
                        {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                    </td>

                    <td>
                        {{ $movement->quantity }}
                    </td>

                    <td>
                        {{ $movement->stock_before }}
                    </td>

                    <td>
                        {{ $movement->stock_after }}
                    </td>

                    <td>
                        {{ $movement->user->name }}
                    </td>

                    <td>
                        {{ $movement->notes ?? '-' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="8">
                        No stock movements found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <br>

    {{ $movements->links() }}

    <br>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>