<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
</head>
<body>

<h1>Edit Category</h1>

@if($errors->any())

    <ul style="color: red;">

        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach

    </ul>

@endif

<form
    action="{{ route('categories.update', $category) }}"
    method="POST"
>

    @csrf
    @method('PUT')

    <label>Name</label>
    <br>

    <input
        type="text"
        name="name"
        value="{{ old('name', $category->name) }}"
    >

    <br><br>

    <label>Description</label>
    <br>

    <textarea name="description">{{ old('description', $category->description) }}</textarea>

    <br><br>

    <label>Parent Category</label>
    <br>

    <select name="parent_id">

        <option value="">
            None / Root Category
        </option>

        @foreach($categories as $parent)

            <option
                value="{{ $parent->id }}"
                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}
            >
                {{ $parent->name }}
            </option>

        @endforeach

    </select>

    <br><br>

    <button type="submit">
        Update Category
    </button>

</form>

<br>

<a href="{{ route('categories.index') }}">
    Back to Categories
</a>

</body>
</html>