@extends('layouts.app')
@section('title', 'Activity Log')
@section('page_title', 'Activity Log')
@section('content')

<div class="page-header">
    <div>
        <div class="page-title">📋 Activity Log</div>
        <div class="page-subtitle">Complete audit trail of all system actions</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="form-group">
        <label class="form-label">Search</label>
        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
               placeholder="Search description…" style="min-width:200px;">
    </div>
    <div class="form-group">
        <label class="form-label">Action</label>
        <select name="action" class="form-select">
            <option value="">All Actions</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select">
            <option value="">All Users</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Module</label>
        <select name="model" class="form-select">
            <option value="">All Modules</option>
            @foreach($models as $model)
                <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>{{ $model }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">From</label>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    </div>
    <div class="form-group">
        <label class="form-label">To</label>
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    </div>
    <div class="form-group" style="align-self:flex-end;">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-muted" style="white-space:nowrap;font-size:12px;">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <span style="color:var(--gray-400);">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        @if($log->user)
                            <div class="fw-semibold" style="font-size:13px;">{{ $log->user->name }}</div>
                            <div style="font-size:11px;color:var(--gray-400);">{{ $log->user->role }}</div>
                        @else
                            <span class="text-muted">System</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $log->badge_class }}">
                            {{ $log->icon }} {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>
                        @if($log->model)
                            <span class="badge badge-gray">{{ $log->model }}</span>
                            @if($log->model_id)
                                <span style="font-size:10px;color:var(--gray-400);">#{{ $log->model_id }}</span>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="max-width:360px;font-size:13px;">{{ $log->description }}</td>
                    <td style="font-size:12px;font-family:monospace;color:var(--gray-400);">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        <div class="empty-state-text">No activity logs yet</div>
                        <div class="empty-state-sub">Activity will appear here as users interact with the system</div>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="pagination-wrapper">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
