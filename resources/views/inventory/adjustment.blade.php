@extends('layouts.app')
@section('title', 'Stock Adjustment')
@section('page_title', 'Manual Stock Adjustment')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Manual Stock Adjustment</div>
        <div class="page-subtitle">Add or remove stock for a product manually</div>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">← Back to Inventory</a>
</div>

<div class="card" style="max-width:550px;">
    <div class="card-header"><span class="card-title">Adjustment Details</span></div>
    <div class="card-body">
        <form method="POST" action="{{ route('inventory.adjustment.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Product *</label>
                <select name="product_id" class="form-select" required id="product-select">
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-unit="{{ $product->unit }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }}) — Stock: {{ $product->stock_quantity }} {{ $product->unit }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Current Stock</label>
                <input type="text" id="current-stock" class="form-control" value="—" readonly
                       style="background:var(--gray-100);cursor:not-allowed;">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Adjustment Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="in"  {{ old('type') === 'in'  ? 'selected' : '' }}>▲ Add Stock (In)</option>
                        <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>▼ Remove Stock (Out)</option>
                    </select>
                    @error('type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-control"
                           value="{{ old('quantity') }}" step="0.01" min="0.01" required>
                    @error('quantity')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Reason *</label>
                <select name="reason" class="form-select" required>
                    <option value="manual_adjustment" {{ old('reason', 'manual_adjustment') === 'manual_adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                    <option value="damage"            {{ old('reason') === 'damage'            ? 'selected' : '' }}>Damaged Goods</option>
                    <option value="return"            {{ old('reason') === 'return'            ? 'selected' : '' }}>Return</option>
                    <option value="correction"        {{ old('reason') === 'correction'        ? 'selected' : '' }}>Inventory Correction</option>
                    <option value="other"             {{ old('reason') === 'other'             ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Optional: explain reason for adjustment…">{{ old('notes') }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-warning">Apply Adjustment</button>
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('product-select').addEventListener('change', function() {
    const opt     = this.selectedOptions[0];
    const stock   = opt?.dataset?.stock ?? '';
    const unit    = opt?.dataset?.unit  ?? '';
    document.getElementById('current-stock').value = stock ? stock + ' ' + unit : '—';
});
</script>
@endpush