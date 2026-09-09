@extends('layouts.app')
@section('title', 'Add Expense')
@section('page_title', 'Record Expense')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">💸 Record New Expense</div>
        <div class="page-subtitle">Log an operating cost or business expenditure</div>
    </div>
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div style="max-width:640px;">
<form method="POST" action="{{ route('expenses.store') }}">
@csrf

<div class="card">
    <div class="card-header"><span class="card-title">Expense Details</span></div>
    <div class="card-body">

        <div class="form-group">
            <label class="form-label">Title / Description *</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title') }}" placeholder="e.g. Monthly Office Rent" required autofocus>
            @error('title')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Amount (Rs.) *</label>
                <input type="number" name="amount" class="form-control"
                       value="{{ old('amount') }}" placeholder="0.00" min="0.01" step="0.01" required>
                @error('amount')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Expense Date *</label>
                <input type="date" name="expense_date" class="form-control"
                       value="{{ old('expense_date', date('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash"  {{ old('payment_method','cash') === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                    <option value="bank"  {{ old('payment_method') === 'bank' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                    <option value="card"  {{ old('payment_method') === 'card' ? 'selected' : '' }}>💳 Card</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Additional details, receipt reference, etc.">{{ old('notes') }}</textarea>
        </div>

        <div class="divider"></div>
        <button type="submit" class="btn btn-danger w-100" style="padding:12px;font-size:15px;">
            💸 Record Expense
        </button>
    </div>
</div>

</form>
</div>

@endsection
