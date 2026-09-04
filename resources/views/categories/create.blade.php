<!DOCTYPE html>
<html>
<head>
    <title>Create Category</title>
</head>
<body>

<h1>Create Category</h1>

@if($errors->any())

    <ul style="color: red;">

        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach

    </ul>

@endif

<form
    action="{{ route('categories.store') }}"
    method="POST"
>

    @csrf

    <label>Name</label>
    <br>

    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
    >

    <br><br>

    <label>Description</label>
    <br>

    <textarea name="description">{{ old('description') }}</textarea>

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
                {{ old('parent_id') == $parent->id ? 'selected' : '' }}
            >
                {{ $parent->name }}
            </option>

        @endforeach

    </select>

    <br><br>

    <button type="submit">
        Create Category
    </button>

</form>

<br>

<a href="{{ route('categories.index') }}">
    Back to Categories
</a>

</body>
</html>