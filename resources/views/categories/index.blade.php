@extends('layouts.app')

@section('title', 'Categories')
@section('page_title', 'Categories')

@section('content')

<div class="page-header">
    <div>
        <div class="page-title">Categories</div>
        <div class="page-subtitle">Organize your product catalog</div>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Add Category</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Parent Category</th>
                    <th>Sub-categories</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td class="text-muted">{{ $category->id }}</td>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td>{{ $category->parent?->name ?? '—' }}</td>
                    <td>
                        @if($category->children->count())
                            <span class="badge badge-primary">{{ $category->children->count() }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-8">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">🗂️</div>
                            <div class="empty-state-text">No categories found</div>
                            <div class="empty-state-sub"><a href="{{ route('categories.create') }}" class="text-primary">Create your first category</a></div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($categories, 'hasPages') && $categories->hasPages())
    <div class="pagination-wrapper">{{ $categories->links() }}</div>
    @endif
</div>

@endsection