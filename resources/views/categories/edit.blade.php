@extends('layouts.app')
@section('title', 'Edit Category')
@section('page_title', 'Edit Category')
@section('content')
<div class="page-header">
    <div class="page-title">Edit: {{ $category->name }}</div>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Back</a>
</div>
<div class="card" style="max-width:500px;">
    <div class="card-header"><span class="card-title">Category Information</span></div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $category->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-select">
                    <option value="">None / Root Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="divider"></div>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection