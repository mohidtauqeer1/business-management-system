<!DOCTYPE html>
<html>
<head>
    <title>Create Purchase</title>
</head>
<body>

<h1>Create Purchase</h1>

@if($errors->any())
    <div style="color: red;">
        <strong>Please fix the following errors:</strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <p style="color: red;">
        {{ session('error') }}
    </p>
@endif

<h2>Purchase Information</h2>

<form method="POST" action="{{ route('purchases.store') }}">

    @csrf

    {{-- Supplier --}}
    <div>
        <label for="supplier_id">
            Supplier
        </label>

        <select name="supplier_id" id="supplier_id" required>

            <option value="">
                Select Supplier
            </option>

            @foreach($suppliers as $supplier)

                <option
                    value="{{ $supplier->id }}"
                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                >
                    {{ $supplier->name }}
                </option>

            @endforeach

        </select>
    </div>

    <br>

    {{-- Purchase Date --}}
    <div>
        <label for="purchase_date">
            Purchase Date
        </label>

        <input
            type="date"
            name="purchase_date"
            id="purchase_date"
            value="{{ old('purchase_date', date('Y-m-d')) }}"
            required
        >
    </div>

    <br>

    {{-- Invoice Number --}}
    <div>
        <label for="invoice_number">
            Invoice Number
        </label>

        <input
            type="text"
            name="invoice_number"
            id="invoice_number"
            value="{{ old('invoice_number') }}"
            required
        >
    </div>

    <hr>

    <h2>Products</h2>

    <table border="1" cellpadding="10" cellspacing="0">

        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody id="items-container">

            <tr class="item-row">

                <td>
                    <select
                        name="items[0][product_id]"
                        class="product"
                        required
                    >

                        <option value="">
                            Select Product
                        </option>

                        @foreach($products as $product)

                            <option value="{{ $product->id }}">
                                {{ $product->name }} -
                                {{ $product->sku }}
                            </option>

                        @endforeach

                    </select>
                </td>

                <td>
                    <input
                        type="number"
                        name="items[0][quantity]"
                        class="quantity"
                        value="1"
                        min="0.01"
                        step="0.01"
                        required
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="items[0][unit_price]"
                        class="unit-price"
                        value="0"
                        min="0"
                        step="0.01"
                        required
                    >
                </td>

                <td>
                    <input
                        type="text"
                        class="subtotal"
                        value="0.00"
                        readonly
                    >
                </td>

                <td>
                    <button
                        type="button"
                        class="remove-row"
                    >
                        Remove
                    </button>
                </td>

            </tr>

        </tbody>

    </table>

    <br>

    <button
        type="button"
        id="add-product"
    >
        + Add Product
    </button>

    <hr>

    <h2>Payment</h2>

    {{-- Total --}}
    <div>
        <strong>
            Total Amount:
        </strong>

        <span id="total-amount">
            0.00
        </span>
    </div>

    <br>

    {{-- Paid Amount --}}
    <div>
        <label for="paid_amount">
            Paid Amount
        </label>

        <input
            type="number"
            name="paid_amount"
            id="paid_amount"
            value="{{ old('paid_amount', 0) }}"
            min="0"
            step="0.01"
            required
        >
    </div>

    <br>

    {{-- Payment Status --}}
    <div>
        <label for="payment_status">
            Payment Status
        </label>

        <select
            name="payment_status"
            id="payment_status"
            required
        >
            <option
                value="paid"
                {{ old('payment_status') == 'paid' ? 'selected' : '' }}
            >
                Paid
            </option>

            <option
                value="partial"
                {{ old('payment_status') == 'partial' ? 'selected' : '' }}
            >
                Partial
            </option>

            <option
                value="unpaid"
                {{ old('payment_status', 'unpaid') == 'unpaid' ? 'selected' : '' }}
            >
                Unpaid
            </option>
        </select>
    </div>

    <br>

    <button type="submit">
        Create Purchase
    </button>

    <a href="{{ route('purchases.index') }}">
        Cancel
    </a>

</form>


<script>

let itemIndex = 1;


/*
|--------------------------------------------------------------------------
| Calculate Row Subtotal
|--------------------------------------------------------------------------
*/

function calculateRow(row) {

    const quantity = parseFloat(
        row.querySelector('.quantity').value
    ) || 0;

    const unitPrice = parseFloat(
        row.querySelector('.unit-price').value
    ) || 0;

    const subtotal = quantity * unitPrice;

    row.querySelector('.subtotal').value =
        subtotal.toFixed(2);

    calculateTotal();
}


/*
|--------------------------------------------------------------------------
| Calculate Total
|--------------------------------------------------------------------------
*/

function calculateTotal() {

    let total = 0;

    document
        .querySelectorAll('.item-row')
        .forEach(function(row) {

            const quantity = parseFloat(
                row.querySelector('.quantity').value
            ) || 0;

            const unitPrice = parseFloat(
                row.querySelector('.unit-price').value
            ) || 0;

            total += quantity * unitPrice;
        });

    document.getElementById('total-amount').textContent =
        total.toFixed(2);
}


/*
|--------------------------------------------------------------------------
| Quantity / Price Changes
|--------------------------------------------------------------------------
*/

document.addEventListener('input', function(event) {

    if (
        event.target.classList.contains('quantity') ||
        event.target.classList.contains('unit-price')
    ) {

        const row = event.target.closest('.item-row');

        calculateRow(row);
    }

});


/*
|--------------------------------------------------------------------------
| Add Product
|--------------------------------------------------------------------------
*/

document
    .getElementById('add-product')
    .addEventListener('click', function() {

        const container =
            document.getElementById('items-container');

        const row =
            document.querySelector('.item-row').cloneNode(true);

        /*
        |--------------------------------------------------------------------------
        | Update input names
        |--------------------------------------------------------------------------
        */

        row.querySelector('.product').name =
            `items[${itemIndex}][product_id]`;

        row.querySelector('.quantity').name =
            `items[${itemIndex}][quantity]`;

        row.querySelector('.unit-price').name =
            `items[${itemIndex}][unit_price]`;

        /*
        |--------------------------------------------------------------------------
        | Reset values
        |--------------------------------------------------------------------------
        */

        row.querySelector('.product').value = '';

        row.querySelector('.quantity').value = 1;

        row.querySelector('.unit-price').value = 0;

        row.querySelector('.subtotal').value = '0.00';

        container.appendChild(row);

        itemIndex++;
    });


/*
|--------------------------------------------------------------------------
| Remove Product
|--------------------------------------------------------------------------
*/

document.addEventListener('click', function(event) {

    if (
        event.target.classList.contains('remove-row')
    ) {

        const rows =
            document.querySelectorAll('.item-row');

        /*
        |--------------------------------------------------------------------------
        | Don't allow removing the last row
        |--------------------------------------------------------------------------
        */

        if (rows.length === 1) {
            alert('At least one product is required.');
            return;
        }

        event.target
            .closest('.item-row')
            .remove();

        calculateTotal();
    }

});


/*
|--------------------------------------------------------------------------
| Initial Calculation
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.item-row')
    .forEach(function(row) {

        calculateRow(row);

    });

</script>

</body>
</html>