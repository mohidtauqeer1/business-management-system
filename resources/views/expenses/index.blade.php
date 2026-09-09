@extends('layouts.app')
@section('title', 'Expenses')
@section('page_title', 'Expense Management')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">💸 Expenses</div>
        <div class="page-subtitle">Track operating costs and business expenditure</div>
    </div>
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">+ Add Expense</a>
</div>

{{-- Monthly Summary Cards --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">💸</div>
        <div>
            <div class="stat-label">This Month Expenses</div>
            <div class="stat-value" style="font-size:20px;">Rs. {{ number_format($monthTotal, 0) }}</div>
            <div class="stat-sub">{{ now()->format('F Y') }}</div>
        </div>
    </div>
    @foreach($categoryTotals->sortDesc()->take(3) as $cat => $total)
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">{{ array_key_exists($cat, $categories) ? explode(' ', $categories[$cat])[0] : '📦' }}</div>
        <div>
            <div class="stat-label">{{ $categories[$cat] ?? ucfirst($cat) }}</div>
            <div class="stat-value" style="font-size:18px;">Rs. {{ number_format($total, 0) }}</div>
            <div class="stat-sub">This month</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="form-group">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">From Date</label>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="form-group">
        <label class="form-label">To Date</label>
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="form-group" style="align-self:flex-end;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th class="text-right">Amount</th>
                    <th>Payment</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td class="fw-semibold" style="font-family:monospace;font-size:12px;">{{ $expense->reference_number }}</td>
                    <td class="fw-semibold">
                        <a href="{{ route('expenses.show', $expense) }}" class="text-primary">{{ $expense->title }}</a>
                    </td>
                    <td>
                        <span class="badge badge-gray">
                            {{ $categories[$expense->category] ?? ucfirst($expense->category) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                    <td class="text-right fw-semibold text-danger">Rs. {{ number_format($expense->amount, 0) }}</td>
                    <td>
                        @if($expense->payment_method === 'cash')
                            <span class="badge badge-success">Cash</span>
                        @elseif($expense->payment_method === 'bank')
                            <span class="badge badge-cyan">Bank</span>
                        @else
                            <span class="badge badge-warning">Card</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $expense->user?->name }}</td>
                    <td>
                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" style="display:inline;"
                              onsubmit="return confirm('Delete this expense?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Del</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-state-icon">💸</div>
                        <div class="empty-state-text">No expenses recorded</div>
                        <div class="empty-state-sub"><a href="{{ route('expenses.create') }}" class="text-primary">Add the first expense</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="pagination-wrapper">{{ $expenses->links() }}</div>
    @endif
</div>

@endsection
