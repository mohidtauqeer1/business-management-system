@extends('layouts.app')
@section('title', 'Edit Expense')
@section('page_title', 'Edit Expense')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">✏️ Edit — {{ $expense->title }}</div>
        <div class="page-subtitle">{{ $expense->reference_number }}</div>
    </div>
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div style="max-width:640px;">
<form method="POST" action="{{ route('expenses.update', $expense) }}">
@csrf @method('PUT')

<div class="card">
    <div class="card-header"><span class="card-title">Expense Details</span></div>
    <div class="card-body">

        <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $expense->title) }}" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-select" required>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Amount *</label>
                <input type="number" name="amount" class="form-control"
                       value="{{ old('amount', $expense->amount) }}" min="0.01" step="0.01" required>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label class="form-label">Expense Date *</label>
                <input type="date" name="expense_date" class="form-control"
                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="payment_method" class="form-select" required>
                    <option value="cash" {{ old('payment_method', $expense->payment_method) === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                    <option value="bank" {{ old('payment_method', $expense->payment_method) === 'bank' ? 'selected' : '' }}>🏦 Bank</option>
                    <option value="card" {{ old('payment_method', $expense->payment_method) === 'card' ? 'selected' : '' }}>💳 Card</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
        </div>

        <div class="divider"></div>
        <button type="submit" class="btn btn-primary w-100" style="padding:12px;">💾 Update Expense</button>
    </div>
</div>

</form>
</div>

@endsection
