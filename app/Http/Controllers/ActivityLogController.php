<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs    = $query->paginate(25)->withQueryString();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        $models  = ActivityLog::distinct()->whereNotNull('model')->pluck('model')->sort()->values();
        $users   = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('activity-logs.index', compact('logs', 'actions', 'models', 'users'));
    }
}
