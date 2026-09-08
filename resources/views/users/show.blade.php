@extends('layouts.app')
@section('title', $user->name)
@section('page_title', 'User Details')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">{{ $user->name }}</div>
        <div class="page-subtitle">User profile</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>
<div class="card" style="max-width:500px;">
    <div class="card-header"><span class="card-title">Account Information</span></div>
    <div class="card-body">
        <table class="table">
            <tr><td class="text-muted" style="width:140px;">Name</td><td class="fw-semibold">{{ $user->name }}</td></tr>
            <tr><td class="text-muted">Email</td><td>{{ $user->email }}</td></tr>
            <tr><td class="text-muted">Phone</td><td>{{ $user->phone ?? '—' }}</td></tr>
            <tr>
                <td class="text-muted">Role</td>
                <td>
                    @php $roleColors = ['admin'=>'badge-danger','manager'=>'badge-warning','cashier'=>'badge-primary','staff'=>'badge-gray']; @endphp
                    <span class="badge {{ $roleColors[$user->role] ?? 'badge-gray' }}">{{ ucfirst($user->role) }}</span>
                </td>
            </tr>
            <tr>
                <td class="text-muted">Status</td>
                <td>
                    @if($user->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-danger">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr><td class="text-muted">Joined</td><td>{{ $user->created_at->format('d M Y') }}</td></tr>
        </table>
    </div>
</div>
@endsection