@extends('layouts.app')
@section('title', 'Process Sale Return')
@section('page_title', 'Create Sale Return')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">↩️ Return Items — {{ $sale->invoice_number }}</div>
        <div class="page-subtitle">
            Customer: {{ $sale->customer?->name ?? 'Walk-in' }}
            &nbsp;·&nbsp;
            Sale Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
        </div>
    </div>
    <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">← Back to Invoice</a>
</div>

@if($errors->any())
<div class="alert alert-danger" style="max-width:860px;margin-bottom:20px;">
    <span class="alert-icon">❌</span>
    <div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<form method="POST" action="{{ route('returns.sales.store', $sale) }}">
@csrf

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start;">

    {{-- Items to Return --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Select Items to Return</span>
            <span class="badge badge-primary">{{ $sale->items->count() }} item(s) in original sale</span>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Sold Qty</th>
                        <th class="text-center">Return Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th>Return Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $item)
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
                                   placeholder="Defective, wrong item…"
                                   style="font-size:12px;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Return Details --}}
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
                              placeholder="General reason for return…">{{ old('reason') }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Internal notes…">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Refund Details</span></div>
            <div class="card-body">
                <div style="background:var(--gray-50);border-radius:8px;padding:14px;margin-bottom:16px;text-align:center;">
                    <div class="stat-label">Return Total</div>
                    <div style="font-size:22px;font-weight:800;color:var(--primary);" id="return-total">Rs. 0.00</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Refund Amount</label>
                    <input type="number" name="refund_amount" id="refund_amount" class="form-control"
                           value="{{ old('refund_amount', 0) }}" min="0" step="0.01">
                    <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Can be less than return total if partial refund</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Refund Status *</label>
                    <select name="refund_status" class="form-select" required>
                        <option value="refunded" {{ old('refund_status','refunded') === 'refunded' ? 'selected' : '' }}>✅ Refunded Now</option>
                        <option value="pending"  {{ old('refund_status') === 'pending'  ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="no_refund"{{ old('refund_status') === 'no_refund'? 'selected' : '' }}>🚫 No Refund</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Refund Method *</label>
                    <select name="refund_method" class="form-select" required>
                        <option value="cash"   {{ old('refund_method','cash') === 'cash'   ? 'selected' : '' }}>Cash</option>
                        <option value="bank"   {{ old('refund_method') === 'bank'   ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="card"   {{ old('refund_method') === 'card'   ? 'selected' : '' }}>Card</option>
                        <option value="credit" {{ old('refund_method') === 'credit' ? 'selected' : '' }}>Store Credit</option>
                    </select>
                </div>

                <div class="divider"></div>
                <button type="submit" class="btn btn-danger w-100" style="font-size:15px;padding:12px;">
                    ↩️ Process Return
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
        const qty = parseFloat(input.value) || 0;
        const price = parseFloat(input.dataset.price) || 0;
        total += qty * price;
    });
    document.getElementById('return-total').textContent = 'Rs. ' + total.toFixed(2);
    document.getElementById('refund_amount').value = total.toFixed(2);
}
document.querySelectorAll('.return-qty').forEach(input => {
    input.addEventListener('input', recalculate);
});
recalculate();
</script>
@endpush
