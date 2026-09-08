@extends('layouts.app')
@section('title', 'Add Customer')
@section('page_title', 'Add Customer')
@section('content')
<div class="page-header">
    <div class="page-title">Add New Customer</div>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">← Back</a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-header"><span class="card-title">Customer Information</span></div>
    <div class="card-body">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Customer Name *</label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}" placeholder="e.g. Ali Ahmed" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ old('phone') }}" placeholder="03001234567">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="customer@example.com">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3"
                          placeholder="Full address…">{{ old('address') }}</textarea>
            </div>
            <div class="divider"></div>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection