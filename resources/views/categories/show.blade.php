@extends('layouts.app')
@section('title', $category->name)
@section('page_title', 'Category Details')
@section('content')
<div class="page-header">
    <div class="page-title">{{ $category->name }}</div>
    <div class="page-header-actions">
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Back</a>
    </div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:900px;">
    <div class="card">
        <div class="card-header"><span class="card-title">Details</span></div>
        <div class="card-body">
            <table class="table">
                <tr><td class="text-muted" style="width:140px;">Name</td><td class="fw-semibold">{{ $category->name }}</td></tr>
                <tr><td class="text-muted">Description</td><td>{{ $category->description ?? '—' }}</td></tr>
                <tr><td class="text-muted">Parent</td><td>{{ $category->parent?->name ?? '— Root' }}</td></tr>
                <tr><td class="text-muted">Products</td><td>{{ $category->products->count() }}</td></tr>
                <tr><td class="text-muted">Sub-categories</td><td>{{ $category->children->count() }}</td></tr>
            </table>
        </div>
    </div>
    @if($category->children->count())
    <div class="card">
        <div class="card-header"><span class="card-title">Sub-categories</span></div>
        <div class="card-body">
            @foreach($category->children as $child)
                <div style="padding:8px 0;border-bottom:1px solid var(--gray-100);">
                    <a href="{{ route('categories.show', $child) }}" class="text-primary fw-semibold">{{ $child->name }}</a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection