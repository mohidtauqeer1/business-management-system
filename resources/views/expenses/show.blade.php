@extends('layouts.app')
@section('title', 'Expense — ' . $expense->reference_number)
@section('page_title', 'Expense Detail')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">💸 {{ $expense->title }}</div>
        <div class="page-subtitle">{{ $expense->reference_number }}</div>
    </div>
    <div class="d-flex gap-8">
        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-warning">✏️ Edit</a>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>

<div style="max-width:600px;">
<div class="card">
    <div class="card-header"><span class="card-title">Expense Details</span></div>
    <div class="card-body">
        <table class="table">
            <tr><td class="text-muted" style="width:160px;">Reference</td><td class="fw-semibold" style="font-family:monospace;">{{ $expense->reference_number }}</td></tr>
            <tr><td class="text-muted">Title</td><td class="fw-semibold">{{ $expense->title }}</td></tr>
            <tr><td class="text-muted">Category</td><td><span class="badge badge-gray">{{ \App\Models\Expense::categories()[$expense->category] ?? ucfirst($expense->category) }}</span></td></tr>
            <tr><td class="text-muted">Amount</td><td class="fw-bold text-danger" style="font-size:18px;">Rs. {{ number_format($expense->amount, 0) }}</td></tr>
            <tr><td class="text-muted">Date</td><td>{{ $expense->expense_date->format('d M Y, l') }}</td></tr>
            <tr><td class="text-muted">Payment</td><td>
                @if($expense->payment_method === 'cash') <span class="badge badge-success">💵 Cash</span>
                @elseif($expense->payment_method === 'bank') <span class="badge badge-cyan">🏦 Bank</span>
                @else <span class="badge badge-warning">💳 Card</span>
                @endif
            </td></tr>
            <tr><td class="text-muted">Recorded By</td><td>{{ $expense->user?->name }}</td></tr>
            <tr><td class="text-muted">Created</td><td class="text-muted">{{ $expense->created_at->format('d M Y H:i') }}</td></tr>
            @if($expense->notes)
            <tr><td class="text-muted">Notes</td><td>{{ $expense->notes }}</td></tr>
            @endif
        </table>
    </div>
</div>
</div>

@endsection
