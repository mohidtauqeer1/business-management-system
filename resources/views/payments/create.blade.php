@extends('layouts.app')
@section('title', 'Record Payment')
@section('page_title', 'Record Payment')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Record Payment</div>
        <div class="page-subtitle">Record a supplier or customer payment</div>
    </div>
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">← Payment History</a>
</div>

<div class="card" style="max-width:620px;">
    <div class="card-header"><span class="card-title">Payment Details</span></div>
    <div class="card-body">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Payment Type *</label>
                <select name="type" id="payment_type" class="form-select" required>
                    <option value="">Select payment type…</option>
                    <option value="supplier_payment" {{ old('type') === 'supplier_payment' ? 'selected' : '' }}>
                        Supplier Payment (Pay a purchase)
                    </option>
                    <option value="customer_payment" {{ old('type') === 'customer_payment' ? 'selected' : '' }}>
                        Customer Payment (Receive from customer)
                    </option>
                </select>
                @error('type')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Supplier Section --}}
            <div id="supplier_section" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Purchase Invoice *</label>
                    <select name="purchase_id" class="form-select">
                        <option value="">Select purchase…</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}"
                                    data-due="{{ max(0, $purchase->total_amount - $purchase->paid_amount) }}"
                                    {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->invoice_number }} —
                                {{ $purchase->supplier->name }} —
                                Due: Rs. {{ number_format(max(0, $purchase->total_amount - $purchase->paid_amount), 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Customer Section --}}
            <div id="customer_section" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Sale Invoice *</label>
                    <select name="sale_id" class="form-select">
                        <option value="">Select sale…</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->id }}"
                                    data-due="{{ max(0, $sale->total_amount - $sale->paid_amount) }}"
                                    {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                {{ $sale->invoice_number }} —
                                {{ $sale->customer?->name ?? 'Walk-in' }} —
                                Due: Rs. {{ number_format(max(0, $sale->total_amount - $sale->paid_amount), 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="due-display" style="display:none;background:var(--warning-light);border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;">
                    <span class="fw-semibold" style="color:var(--warning-dark);">Outstanding Amount:</span>
                    <span class="fw-bold" style="color:var(--warning-dark);font-size:16px;" id="due-amount">Rs. 0.00</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" id="amount" class="form-control"
                           value="{{ old('amount') }}" step="0.01" min="0.01" required>
                    @error('amount')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash"   {{ old('payment_method', 'cash') === 'cash'   ? 'selected' : '' }}>Cash</option>
                        <option value="bank"   {{ old('payment_method') === 'bank'   ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card"   {{ old('payment_method') === 'card'   ? 'selected' : '' }}>Card</option>
                        <option value="online" {{ old('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" name="reference_number" class="form-control"
                       value="{{ old('reference_number') }}" placeholder="Cheque/transaction number (optional)">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Optional notes…">{{ old('notes') }}</textarea>
            </div>

            <div class="divider"></div>
            <button type="submit" class="btn btn-success w-100" style="font-size:15px;padding:12px;">
                💰 Record Payment
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const typeSelect       = document.getElementById('payment_type');
const supplierSection  = document.getElementById('supplier_section');
const customerSection  = document.getElementById('customer_section');
const dueDisplay       = document.getElementById('due-display');
const dueAmount        = document.getElementById('due-amount');
const amountInput      = document.getElementById('amount');

function updateSections() {
    const val = typeSelect.value;
    supplierSection.style.display = val === 'supplier_payment' ? 'block' : 'none';
    customerSection.style.display = val === 'customer_payment' ? 'block' : 'none';
    dueDisplay.style.display = 'none';
}

function updateDue(selectEl) {
    const opt = selectEl.selectedOptions[0];
    const due = parseFloat(opt?.dataset?.due || 0);
    if (opt && opt.value && due > 0) {
        dueDisplay.style.display = 'block';
        dueAmount.textContent = 'Rs. ' + due.toFixed(2);
        amountInput.value = due.toFixed(2);
    } else {
        dueDisplay.style.display = 'none';
    }
}

typeSelect.addEventListener('change', updateSections);

document.querySelector('select[name="purchase_id"]').addEventListener('change', function() {
    updateDue(this);
});
document.querySelector('select[name="sale_id"]').addEventListener('change', function() {
    updateDue(this);
});

// Initialize on load
updateSections();
@if(old('type')) typeSelect.dispatchEvent(new Event('change')); @endif
</script>
@endpush