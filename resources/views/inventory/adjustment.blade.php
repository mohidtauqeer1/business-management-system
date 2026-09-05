<!DOCTYPE html>
<html>
<head>
    <title>Manual Stock Adjustment</title>
</head>
<body>

    <h1>Business Management System</h1>

    <h2>Manual Stock Adjustment</h2>

    <p>
        Welcome, {{ auth()->user()->name }}
        |
        Role: {{ auth()->user()->role }}
    </p>

    <a href="{{ route('inventory.index') }}">Inventory</a>
    |
    <a href="{{ route('inventory.movements') }}">Stock History</a>
    |
    <a href="{{ route('inventory.low-stock') }}">Low Stock</a>

    <hr>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('inventory.adjustment.store') }}">

        @csrf

        <div>
            <label>Product:</label>

            <select name="product_id" required>
                <option value="">Select Product</option>

                @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                        — Current Stock: {{ $product->stock_quantity }}
                    </option>
                @endforeach
            </select>
        </div>

        <br>

        <div>
            <label>Adjustment Type:</label>

            <select name="type" required>
                <option value="">Select Type</option>
                <option value="increase">Increase Stock</option>
                <option value="decrease">Decrease Stock</option>
            </select>
        </div>

        <br>

        <div>
            <label>Quantity:</label>

            <input
                type="number"
                name="quantity"
                step="0.01"
                min="0.01"
                required
            >
        </div>

        <br>

        <div>
            <label>Reason / Notes:</label>

            <br>

            <textarea
                name="notes"
                rows="4"
                cols="50"
                placeholder="Example: Physical stock count correction"
            ></textarea>
        </div>

        <br>

        <button type="submit">
            Adjust Stock
        </button>

    </form>

    <br>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

</body>
</html>