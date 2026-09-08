@extends('layouts.app')
@section('title', 'Suppliers')
@section('page_title', 'Suppliers')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Suppliers</div>
        <div class="page-subtitle">Manage your supply partners</div>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Add Supplier</a>
</div>

<form method="GET" action="{{ route('suppliers.index') }}">
    <div class="filter-bar">
        <div class="form-group">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" style="min-width:220px;"
                   placeholder="Name, contact, phone, email…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td class="text-muted">{{ $supplier->id }}</td>
                    <td class="fw-semibold">{{ $supplier->name }}</td>
                    <td>{{ $supplier->contact_person ?? '—' }}</td>
                    <td>{{ $supplier->phone ?? '—' }}</td>
                    <td>{{ $supplier->email ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-8">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                  onsubmit="return confirm('Delete this supplier?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-icon">🏭</div>
                        <div class="empty-state-text">No suppliers found</div>
                        <div class="empty-state-sub"><a href="{{ route('suppliers.create') }}" class="text-primary">Add your first supplier</a></div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="pagination-wrapper">{{ $suppliers->links() }}</div>
    @endif
</div>

@endsection