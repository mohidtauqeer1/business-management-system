<!DOCTYPE html>
<html>
<head>
    <title>Create Customer</title>
</head>
<body>

<h1>Create Customer</h1>

@if($errors->any())
    <ul style="color:red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('customers.store') }}">

    @csrf

    <label>Name</label><br>

    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        required
    >

    <br><br>

    <label>Phone</label><br>

    <input
        type="text"
        name="phone"
        value="{{ old('phone') }}"
    >

    <br><br>

    <label>Email</label><br>

    <input
        type="email"
        name="email"
        value="{{ old('email') }}"
    >

    <br><br>

    <label>Address</label><br>

    <textarea name="address">{{ old('address') }}</textarea>

    <br><br>

    <button type="submit">
        Create Customer
    </button>

    <a href="{{ route('customers.index') }}">
        Cancel
    </a>

</form>

</body>
</html>