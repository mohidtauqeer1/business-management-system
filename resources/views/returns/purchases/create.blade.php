@extends('layouts.app')
@section('title', 'Process Purchase Return')
@section('page_title', 'Create Purchase Return')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">🔄 Return to Supplier — {{ $purchase->invoice_number }}</div>
        <div class="page-subtitle">
            Supplier: {{ $purchase->supplier?->name ?? '—' }}
            &nbsp;·&nbsp;
            Purchase Date: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
        </div>
    </div>
    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">← Back to Invoice</a>
</div>

@if($errors->any())
<div class="alert alert-danger" style="max-width:860px;margin-bottom:20px;">
    <span class="alert-icon">❌</span>
    <div>
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
</div>
@endif

<form method="POST" action="{{ route('returns.purchases.store', $purchase) }}">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Select Items to Return</span>
            <span class="badge badge-warning">Stock will be decreased</span>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Purchased Qty</th>
                        <th class="text-center">Return Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                    <tr>
                        <td class="fw-semibold">{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
                        <td class="text-center" style="width:110px;">
                            <input type="number"
                                   name="items[{{ $index }}][quantity]"
                                   class="form-control return-qty text-center"
                                   value="0"
                                   min="0"
                                   max="{{ $item->quantity }}"
                                   step="0.01"
                                   data-price="{{ $item->unit_price }}"
                                   style="width:90px;margin:0 auto;">
                        </td>
                        <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td style="width:160px;">
                            <input type="text"
                                   name="items[{{ $index }}][reason]"
                                   class="form-control"
                                   placeholder="Damaged, wrong product…"
                                   style="font-size:12px;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><span class="card-title">Return Details</span></div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Return Date *</label>
                    <input type="date" name="return_date" class="form-control"
                           value="{{ old('return_date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Overall Reason</label>
                    <textarea name="reason" class="form-control" rows="2"
                              placeholder="Defective, wrong order…">{{ old('reason') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Credit Details</span></div>
            <div class="card-body">
                <div style="background:var(--warning-light);border-radius:8px;padding:14px;margin-bottom:16px;text-align:center;">
                    <div class="stat-label">Return Total</div>
                    <div style="font-size:22px;font-weight:800;color:var(--warning-dark);" id="return-total">Rs. 0.00</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Credit Amount from Supplier</label>
                    <input type="number" name="credit_amount" id="credit_amount" class="form-control"
                           value="{{ old('credit_amount', 0) }}" min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label class="form-label">Credit Status *</label>
                    <select name="credit_status" class="form-select" required>
                        <option value="pending"   {{ old('credit_status','pending')  === 'pending'    ? 'selected' : '' }}>⏳ Pending from Supplier</option>
                        <option value="credited"  {{ old('credit_status') === 'credited'  ? 'selected' : '' }}>✅ Already Credited</option>
                        <option value="no_credit" {{ old('credit_status') === 'no_credit' ? 'selected' : '' }}>🚫 No Credit</option>
                    </select>
                </div>

                <div class="divider"></div>
                <button type="submit" class="btn btn-warning w-100" style="font-size:15px;padding:12px;">
                    🔄 Process Return
                </button>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
function recalculate() {
    let total = 0;
    document.querySelectorAll('.return-qty').forEach(input => {
        total += (parseFloat(input.value) || 0) * (parseFloat(input.dataset.price) || 0);
    });
    document.getElementById('return-total').textContent = 'Rs. ' + total.toFixed(2);
    document.getElementById('credit_amount').value = total.toFixed(2);
}
document.querySelectorAll('.return-qty').forEach(input => input.addEventListener('input', recalculate));
recalculate();
</script>
@endpush
