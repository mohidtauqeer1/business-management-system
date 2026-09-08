@extends('layouts.app')
@section('title', 'Users')
@section('page_title', 'User Management')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">User Management</div>
        <div class="page-subtitle">Manage system users and their roles</div>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Add User</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '—' }}</td>
                    <td>
                        @php
                            $roleColors = ['admin'=>'badge-danger','manager'=>'badge-warning','cashier'=>'badge-primary','staff'=>'badge-gray'];
                        @endphp
                        <span class="badge {{ $roleColors[$user->role] ?? 'badge-gray' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-8">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state"><div class="empty-state-text">No users found</div></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="pagination-wrapper">{{ $users->links() }}</div>
    @endif
</div>

@endsection