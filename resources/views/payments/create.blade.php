<!DOCTYPE html>
<html>
<head>
    <title>Record Payment</title>
</head>
<body>

    <h1>Business Management System</h1>

    <h2>Record Payment</h2>

    <p>
        Welcome, {{ auth()->user()->name }}
        |
        Role: {{ auth()->user()->role }}
    </p>

    <a href="{{ route('payments.index') }}">Payment History</a>

    <hr>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('payments.store') }}">

        @csrf

        <label>Payment Type:</label>

        <select name="type" id="payment_type" required>
            <option value="">Select Type</option>
            <option value="supplier_payment">
                Supplier Payment
            </option>
            <option value="customer_payment">
                Customer Payment
            </option>
        </select>

        <br><br>

        <div id="supplier_section">

            <label>Supplier:</label>

            <select name="supplier_id">
                <option value="">Select Supplier</option>

                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>

        </div>

        <div id="customer_section">

            <label>Customer:</label>

            <select name="customer_id">
                <option value="">Select Customer</option>

                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">
                        {{ $customer->name }}
                        — Balance: {{ $customer->credit_balance }}
                    </option>
                @endforeach
            </select>

        </div>

        <br>

        <label>Amount:</label>

        <input
            type="number"
            name="amount"
            step="0.01"
            min="0.01"
            required
        >

        <br><br>

        <label>Payment Method:</label>

        <select name="payment_method" required>
            <option value="cash">Cash</option>
            <option value="bank">Bank</option>
            <option value="card">Card</option>
            <option value="online">Online</option>
        </select>

        <br><br>

        <label>Reference Number:</label>

        <input
            type="text"
            name="reference_number"
            maxlength="100"
        >

        <br><br>

        <label>Notes:</label>

        <br>

        <textarea
            name="notes"
            rows="4"
            cols="50"
        ></textarea>

        <br><br>

        <button type="submit">
            Record Payment
        </button>

    </form>

    <script>
        const type = document.getElementById('payment_type');
        const supplierSection = document.getElementById('supplier_section');
        const customerSection = document.getElementById('customer_section');

        function updateSections() {

            if (type.value === 'supplier_payment') {
                supplierSection.style.display = 'block';
                customerSection.style.display = 'none';
            } else if (type.value === 'customer_payment') {
                supplierSection.style.display = 'none';
                customerSection.style.display = 'block';
            } else {
                supplierSection.style.display = 'none';
                customerSection.style.display = 'none';
            }
        }

        type.addEventListener('change', updateSections);

        updateSections();
    </script>

</body>
</html>