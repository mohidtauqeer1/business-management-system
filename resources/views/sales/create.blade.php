<!DOCTYPE html>
<html>
<head>
    <title>Create Sale</title>
</head>
<body>

<h1>Create Sale</h1>

@if(session('success'))
    <div>
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div>
        <strong>Please fix the following errors:</strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('sales.store') }}">

    @csrf

    <h3>Sale Information</h3>

    {{-- Customer --}}
    <div>
        <label>Customer</label>

        <select name="customer_id">
            <option value="">Walk-in Customer</option>

            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">
                    {{ $customer->name }} - {{ $customer->phone }}
                </option>
            @endforeach

        </select>
    </div>

    <br>

    {{-- Sale Date --}}
    <div>
        <label>Sale Date</label>

        <input
            type="date"
            name="sale_date"
            value="{{ old('sale_date', now()->toDateString()) }}"
            required
        >
    </div>

    <br>

    {{-- Invoice --}}
    <div>
        <label>Invoice Number</label>

        <input
            type="text"
            name="invoice_number"
            value="{{ old('invoice_number') }}"
            required
        >
    </div>

    <hr>

    <h3>Products</h3>

    <table border="1" cellpadding="8">

        <thead>
            <tr>
                <th>Product</th>
                <th>Available Stock</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Discount</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody id="itemsBody">

            <tr class="item-row">

                <td>
                    <select
                        name="items[0][product_id]"
                        class="product"
                        required
                    >
                        <option value="">Select Product</option>

                        @foreach($products as $product)
                            <option
                                value="{{ $product->id }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-price="{{ $product->selling_price }}"
                            >
                                {{ $product->name }} - {{ $product->sku }}
                            </option>
                        @endforeach

                    </select>
                </td>

                <td>
                    <span class="available-stock">
                        -
                    </span>
                </td>

                <td>
                    <input
                        type="number"
                        name="items[0][quantity]"
                        class="quantity"
                        min="0.01"
                        step="0.01"
                        value="1"
                        required
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="items[0][unit_price]"
                        class="unit-price"
                        min="0"
                        step="0.01"
                        value="0"
                        required
                    >
                </td>

                <td>
                    <input
                        type="number"
                        name="items[0][discount]"
                        class="item-discount"
                        min="0"
                        step="0.01"
                        value="0"
                    >
                </td>

                <td>
                    <span class="subtotal">
                        0.00
                    </span>
                </td>

                <td>
                    <button type="button" class="remove-row">
                        Remove
                    </button>
                </td>

            </tr>

        </tbody>

    </table>

    <br>

    <button type="button" id="addRow">
        + Add Product
    </button>

    <hr>

    <h3>Payment</h3>

    {{-- Overall Discount --}}
    <div>
        <label>Overall Discount</label>

        <input
            type="number"
            name="discount"
            id="discount"
            min="0"
            step="0.01"
            value="0"
        >
    </div>

    <br>

    {{-- Tax --}}
    <div>
        <label>Tax</label>

        <input
            type="number"
            name="tax"
            id="tax"
            min="0"
            step="0.01"
            value="0"
        >
    </div>

    <br>

    <p>
        Items Total:
        <strong id="itemsTotal">0.00</strong>
    </p>

    <p>
        Grand Total:
        <strong id="grandTotal">0.00</strong>
    </p>

    <div>
        <label>Paid Amount</label>

        <input
            type="number"
            name="paid_amount"
            id="paidAmount"
            min="0"
            step="0.01"
            value="0"
            required
        >
    </div>

    <br>

    <div>
        <label>Payment Method</label>

        <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
        </select>
    </div>

    <br>

    <button type="submit">
        Create Sale
    </button>

</form>


<script>

let rowIndex = 1;

const itemsBody = document.getElementById('itemsBody');
const addRowButton = document.getElementById('addRow');


// Add product row
addRowButton.addEventListener('click', function () {

    const row = document.createElement('tr');

    row.classList.add('item-row');

    row.innerHTML = `
        <td>
            <select
                name="items[${rowIndex}][product_id]"
                class="product"
                required
            >
                <option value="">Select Product</option>

                @foreach($products as $product)
                    <option
                        value="{{ $product->id }}"
                        data-stock="{{ $product->stock_quantity }}"
                        data-price="{{ $product->selling_price }}"
                    >
                        {{ $product->name }} - {{ $product->sku }}
                    </option>
                @endforeach

            </select>
        </td>

        <td>
            <span class="available-stock">-</span>
        </td>

        <td>
            <input
                type="number"
                name="items[${rowIndex}][quantity]"
                class="quantity"
                min="0.01"
                step="0.01"
                value="1"
                required
            >
        </td>

        <td>
            <input
                type="number"
                name="items[${rowIndex}][unit_price]"
                class="unit-price"
                min="0"
                step="0.01"
                value="0"
                required
            >
        </td>

        <td>
            <input
                type="number"
                name="items[${rowIndex}][discount]"
                class="item-discount"
                min="0"
                step="0.01"
                value="0"
            >
        </td>

        <td>
            <span class="subtotal">0.00</span>
        </td>

        <td>
            <button type="button" class="remove-row">
                Remove
            </button>
        </td>
    `;

    itemsBody.appendChild(row);

    rowIndex++;

    calculateTotal();
});


// Product selection
document.addEventListener('change', function (event) {

    if (event.target.classList.contains('product')) {

        const row = event.target.closest('.item-row');

        const selectedOption =
            event.target.options[event.target.selectedIndex];

        const stock =
            selectedOption.dataset.stock;

        const price =
            selectedOption.dataset.price;

        row.querySelector('.available-stock').textContent =
            stock || '-';

        if (price) {
            row.querySelector('.unit-price').value =
                price;
        }

        calculateTotal();
    }

});


// Calculate row subtotal
function calculateRowSubtotal(row)
{
    const quantity =
        parseFloat(row.querySelector('.quantity').value) || 0;

    const unitPrice =
        parseFloat(row.querySelector('.unit-price').value) || 0;

    const discount =
        parseFloat(row.querySelector('.item-discount').value) || 0;

    const gross =
        quantity * unitPrice;

    const subtotal =
        Math.max(gross - discount, 0);

    row.querySelector('.subtotal').textContent =
        subtotal.toFixed(2);

    return subtotal;
}


// Calculate totals
function calculateTotal()
{
    let itemsTotal = 0;

    document.querySelectorAll('.item-row').forEach(function(row) {

        itemsTotal += calculateRowSubtotal(row);

    });

    document.getElementById('itemsTotal').textContent =
        itemsTotal.toFixed(2);

    const discount =
        parseFloat(document.getElementById('discount').value) || 0;

    const tax =
        parseFloat(document.getElementById('tax').value) || 0;

    const grandTotal =
        Math.max(itemsTotal - discount + tax, 0);

    document.getElementById('grandTotal').textContent =
        grandTotal.toFixed(2);
}


// Input changes
document.addEventListener('input', function(event) {

    if (
        event.target.classList.contains('quantity') ||
        event.target.classList.contains('unit-price') ||
        event.target.classList.contains('item-discount') ||
        event.target.id === 'discount' ||
        event.target.id === 'tax'
    ) {
        calculateTotal();
    }

});


// Remove row
document.addEventListener('click', function(event) {

    if (event.target.classList.contains('remove-row')) {

        const rows =
            document.querySelectorAll('.item-row');

        if (rows.length === 1) {
            alert('At least one product is required.');
            return;
        }

        event.target.closest('.item-row').remove();

        calculateTotal();
    }

});


calculateTotal();

</script>

</body>
</html>