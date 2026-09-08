@extends('layouts.app')
@section('title', 'Add Category')
@section('page_title', 'Add Category')
@section('content')
<div class="page-header">
    <div class="page-title">Add New Category</div>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">← Back</a>
</div>
<div class="card" style="max-width:500px;">
    <div class="card-header"><span class="card-title">Category Information</span></div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Category Name *</label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       value="{{ old('name') }}" placeholder="e.g. Electronics" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Optional description…">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Parent Category</label>
                <select name="parent_id" class="form-select">
                    <option value="">None / Root Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="divider"></div>
            <div class="d-flex gap-8">
                <button type="submit" class="btn btn-primary">Save Category</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection