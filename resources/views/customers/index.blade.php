@extends('layouts.app')
@section('title', 'Customers')
@section('page_title', 'Customers')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Customers</div>
        <div class="page-subtitle">Manage your customer accounts</div>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Add Customer</a>
</div>

<form method="GET" action="{{ route('customers.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" style="min-width:220px;"
                   placeholder="Name, phone, email…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Customer Name</th><th>Phone</th><th>Email</th><th>Credit Balance</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="text-muted">{{ $customer->id }}</td>
                    <td class="fw-semibold">{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '—' }}</td>
                    <td>{{ $customer->email ?? '—' }}</td>
                    <td>
                        @if($customer->credit_balance > 0)
                            <span class="badge badge-warning">Rs. {{ number_format($customer->credit_balance, 0) }}</span>
                        @else
                            <span class="text-muted">Rs. 0</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-8">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                  onsubmit="return confirm('Delete this customer?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <div class="empty-state-text">No customers found</div>
                        <div class="empty-state-sub"><a href="{{ route('customers.create') }}" class="text-primary">Add your first customer</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="pagination-wrapper">{{ $customers->links() }}</div>
    @endif
</div>

@endsection