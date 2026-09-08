@extends('layouts.app')
@section('title', 'New Sale')
@section('page_title', 'Create Sale')
@section('breadcrumb')
    <a href="{{ route('sales.index') }}">Sales</a> › New Sale
@endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">New Sale</div>
        <div class="page-subtitle">Create a new sales transaction</div>
    </div>
    <a href="{{ route('sales.index') }}" class="btn btn-secondary">← Back to Sales</a>
</div>

<form method="POST" action="{{ route('sales.store') }}" id="sale-form">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    {{-- Left: Items --}}
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Sale Items</span>
                <button type="button" id="add-product" class="btn btn-primary btn-sm">+ Add Row</button>
            </div>
            <div class="card-body" style="padding:12px;">
                <div id="items-container">
                    <div class="item-row" style="display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1fr auto;gap:8px;align-items:end;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:12px;margin-bottom:8px;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Product</label>
                            <select name="items[0][product_id]" class="form-select product" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock_quantity }}">
                                        {{ $product->name }} (Stock: {{ $product->stock_quantity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Qty</label>
                            <input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Unit Price</label>
                            <input type="number" name="items[0][unit_price]" class="form-control unit-price" value="0" min="0" step="0.01" required>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Discount</label>
                            <input type="number" name="items[0][discount]" class="form-control item-discount" value="0" min="0" step="0.01">
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control subtotal" value="0.00" readonly style="background:var(--gray-100);">
                        </div>
                        <div>
                            <label class="form-label" style="visibility:hidden;">X</label>
                            <button type="button" class="remove-item-btn" title="Remove">✕</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Header & Payment --}}
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><span class="card-title">Sale Info</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Invoice Number *</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" name="invoice_number" class="form-control"
                               value="{{ old('invoice_number', $nextInvoiceNumber) }}" required
                               style="font-family:monospace;font-weight:600;letter-spacing:0.5px;">
                        <span style="font-size:11px;color:var(--gray-400);white-space:nowrap;">Auto-generated</span>
                    </div>
                    @error('invoice_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Sale Date *</label>
                    <input type="date" name="sale_date" class="form-control"
                           value="{{ old('sale_date', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Payment</span></div>
            <div class="card-body">
                <div style="background:var(--gray-50);border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px;">
                    <div style="display:flex;justify-content:space-between;padding:4px 0;">
                        <span>Items Total:</span>
                        <span id="items-total">Rs. 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:4px 0;">
                        <span>Overall Discount:</span>
                        <span id="display-discount">Rs. 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:4px 0;">
                        <span>Tax:</span>
                        <span id="display-tax">Rs. 0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0 4px;border-top:1px solid var(--gray-200);margin-top:4px;font-weight:700;font-size:15px;">
                        <span>Grand Total:</span>
                        <span id="total-amount">Rs. 0.00</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Overall Discount (Rs.)</label>
                    <input type="number" name="discount" id="discount" class="form-control"
                           value="{{ old('discount', 0) }}" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Tax (Rs.)</label>
                    <input type="number" name="tax" id="tax" class="form-control"
                           value="{{ old('tax', 0) }}" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="card">Card</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Paid Amount</label>
                    <input type="number" name="paid_amount" id="paid_amount" class="form-control"
                           value="{{ old('paid_amount', 0) }}" min="0" step="0.01">
                </div>

                <div class="divider"></div>
                <button type="submit" class="btn btn-primary w-100">Save Sale</button>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
let itemIndex = 1;

function getProductsData() {
    return @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name . ' (Stock: ' . $p->stock_quantity . ')', 'price' => $p->selling_price]));
}

function calculateRow(row) {
    const qty      = parseFloat(row.querySelector('.quantity').value)       || 0;
    const price    = parseFloat(row.querySelector('.unit-price').value)     || 0;
    const discount = parseFloat(row.querySelector('.item-discount').value)  || 0;
    const sub      = Math.max(0, (qty * price) - discount);
    row.querySelector('.subtotal').value = sub.toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let itemsTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        itemsTotal += parseFloat(row.querySelector('.subtotal').value) || 0;
    });
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax      = parseFloat(document.getElementById('tax').value)      || 0;
    const total    = Math.max(0, itemsTotal - discount) + tax;

    document.getElementById('items-total').textContent    = 'Rs. ' + itemsTotal.toFixed(2);
    document.getElementById('display-discount').textContent = 'Rs. ' + discount.toFixed(2);
    document.getElementById('display-tax').textContent    = 'Rs. ' + tax.toFixed(2);
    document.getElementById('total-amount').textContent   = 'Rs. ' + total.toFixed(2);
}

function makeRow(index) {
    const pData = getProductsData();
    let opts = '<option value="">Select Product</option>';
    pData.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`; });

    const div = document.createElement('div');
    div.className = 'item-row';
    div.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 1.2fr 1fr 1fr auto;gap:8px;align-items:end;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:12px;margin-bottom:8px;';
    div.innerHTML = `
        <div class="form-group" style="margin:0;"><label class="form-label">Product</label>
            <select name="items[${index}][product_id]" class="form-select product" required>${opts}</select></div>
        <div class="form-group" style="margin:0;"><label class="form-label">Qty</label>
            <input type="number" name="items[${index}][quantity]" class="form-control quantity" value="1" min="0.01" step="0.01" required></div>
        <div class="form-group" style="margin:0;"><label class="form-label">Unit Price</label>
            <input type="number" name="items[${index}][unit_price]" class="form-control unit-price" value="0" min="0" step="0.01" required></div>
        <div class="form-group" style="margin:0;"><label class="form-label">Discount</label>
            <input type="number" name="items[${index}][discount]" class="form-control item-discount" value="0" min="0" step="0.01"></div>
        <div class="form-group" style="margin:0;"><label class="form-label">Subtotal</label>
            <input type="text" class="form-control subtotal" value="0.00" readonly style="background:var(--gray-100);"></div>
        <div><label class="form-label" style="visibility:hidden;">X</label>
            <button type="button" class="remove-item-btn" title="Remove">✕</button></div>`;
    return div;
}

document.getElementById('add-product').addEventListener('click', () => {
    document.getElementById('items-container').appendChild(makeRow(itemIndex++));
});

document.addEventListener('change', e => {
    if (e.target.classList.contains('product')) {
        const row   = e.target.closest('.item-row');
        const price = e.target.selectedOptions[0]?.dataset?.price || 0;
        row.querySelector('.unit-price').value = price;
        calculateRow(row);
    }
});

document.addEventListener('input', e => {
    if (['quantity','unit-price','item-discount'].some(c => e.target.classList.contains(c))) {
        calculateRow(e.target.closest('.item-row'));
    }
    if (e.target.id === 'discount' || e.target.id === 'tax') calculateTotal();
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-item-btn')) {
        if (document.querySelectorAll('.item-row').length === 1) {
            alert('At least one product is required.'); return;
        }
        e.target.closest('.item-row').remove();
        calculateTotal();
    }
});

document.querySelectorAll('.item-row').forEach(row => calculateRow(row));
</script>
@endpush