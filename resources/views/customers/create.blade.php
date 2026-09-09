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

            <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:16px;margin-bottom:16px;">
                <div style="font-weight:700;font-size:13px;margin-bottom:12px;color:var(--gray-700);">🛡️ Credit Limit</div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                    <input type="checkbox" name="credit_limit_enabled" value="1"
                           id="cl_enabled"
                           {{ old('credit_limit_enabled') ? 'checked' : '' }}
                           onchange="document.getElementById('cl_box').style.display=this.checked?'block':'none'">
                    <label for="cl_enabled" style="font-size:13px;cursor:pointer;">Enable credit limit for this customer</label>
                </div>
                <div id="cl_box" style="display:{{ old('credit_limit_enabled') ? 'block' : 'none' }};">
                    <label class="form-label">Credit Limit (Rs.)</label>
                    <input type="number" name="credit_limit" class="form-control"
                           value="{{ old('credit_limit', 0) }}" min="0" step="1000">
                    <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">Set to 0 to use the global default from Settings.</div>
                </div>
            </div>

            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
@endsection