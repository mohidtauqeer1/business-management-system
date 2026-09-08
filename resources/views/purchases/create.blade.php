@extends('layouts.app')
@section('title', 'New Purchase')
@section('page_title', 'Create Purchase')
@section('breadcrumb')
    <a href="{{ route('purchases.index') }}">Purchases</a> › New Purchase
@endsection

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">New Purchase</div>
        <div class="page-subtitle">Record a purchase from a supplier</div>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">← Back to Purchases</a>
</div>

<form method="POST" action="{{ route('purchases.store') }}" id="purchase-form">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    {{-- Left: Items --}}
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title">Purchase Items</span>
                <button type="button" id="add-product" class="btn btn-primary btn-sm">+ Add Row</button>
            </div>
            <div class="card-body" style="padding:12px;">

                <div id="items-container">
                    <div class="item-row" style="display:grid;grid-template-columns:2.5fr 1fr 1.2fr 1fr auto;gap:8px;align-items:end;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:12px;margin-bottom:8px;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label">Product</label>
                            <select name="items[0][product_id]" class="form-select product" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                        {{ $product->name }} ({{ $product->sku }})
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
            <div class="card-header"><span class="card-title">Purchase Info</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="form-error">{{ $message }}</div>@enderror
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
                    <label class="form-label">Purchase Date *</label>
                    <input type="date" name="purchase_date" class="form-control"
                           value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Payment</span></div>
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:var(--gray-50);border-radius:8px;margin-bottom:16px;">
                    <span class="fw-semibold">Total Amount:</span>
                    <span class="fw-bold" id="total-amount" style="font-size:18px;color:var(--gray-900);">Rs. 0.00</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Paid Amount</label>
                    <input type="number" name="paid_amount" id="paid_amount" class="form-control"
                           value="{{ old('paid_amount', 0) }}" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="unpaid"  {{ old('payment_status', 'unpaid') === 'unpaid'  ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ old('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid"    {{ old('payment_status') === 'paid'    ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="divider"></div>
                <button type="submit" class="btn btn-primary w-100">Save Purchase</button>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
let itemIndex = 1;

const productsData = @json($products->mapWithKeys(fn($p) => [$p->id => ['price' => $p->purchase_price, 'name' => $p->name]]));

function calculateRow(row) {
    const qty   = parseFloat(row.querySelector('.quantity').value)   || 0;
    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
    const sub   = qty * price;
    row.querySelector('.subtotal').value = sub.toFixed(2);
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.quantity').value)   || 0;
        const price = parseFloat(row.querySelector('.unit-price').value) || 0;
        total += qty * price;
    });
    document.getElementById('total-amount').textContent = 'Rs. ' + total.toFixed(2);
}

function makeRow(index) {
    const productOptions = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name . ' (' . $p->sku . ')', 'price' => $p->purchase_price]));
    let opts = '<option value="">Select Product</option>';
    productOptions.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`; });

    const div = document.createElement('div');
    div.className = 'item-row';
    div.style.cssText = 'display:grid;grid-template-columns:2.5fr 1fr 1.2fr 1fr auto;gap:8px;align-items:end;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:12px;margin-bottom:8px;';
    div.innerHTML = `
        <div class="form-group" style="margin:0;">
            <label class="form-label">Product</label>
            <select name="items[${index}][product_id]" class="form-select product" required>${opts}</select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Qty</label>
            <input type="number" name="items[${index}][quantity]" class="form-control quantity" value="1" min="0.01" step="0.01" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Unit Price</label>
            <input type="number" name="items[${index}][unit_price]" class="form-control unit-price" value="0" min="0" step="0.01" required>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Subtotal</label>
            <input type="text" class="form-control subtotal" value="0.00" readonly style="background:var(--gray-100);">
        </div>
        <div>
            <label class="form-label" style="visibility:hidden;">X</label>
            <button type="button" class="remove-item-btn" title="Remove">✕</button>
        </div>`;
    return div;
}

document.getElementById('add-product').addEventListener('click', () => {
    document.getElementById('items-container').appendChild(makeRow(itemIndex++));
});

document.addEventListener('change', e => {
    if (e.target.classList.contains('product')) {
        const row = e.target.closest('.item-row');
        const opt = e.target.selectedOptions[0];
        const price = opt?.dataset?.price || 0;
        row.querySelector('.unit-price').value = price;
        calculateRow(row);
    }
});

document.addEventListener('input', e => {
    if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
        calculateRow(e.target.closest('.item-row'));
    }
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-item-btn')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 1) { alert('At least one product is required.'); return; }
        e.target.closest('.item-row').remove();
        calculateTotal();
    }
});

document.querySelectorAll('.item-row').forEach(row => calculateRow(row));
</script>
@endpush