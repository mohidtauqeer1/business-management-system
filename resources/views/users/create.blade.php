<!DOCTYPE html>
<html>
<head>
    <title>Create User</title>
</head>
<body>

    <h1>Create User</h1>

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('users.store') }}" method="POST">

        @csrf

        <label>Name</label>
        <br>
        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
        >

        <br><br>

        <label>Email</label>
        <br>
        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
        >

        <br><br>

        <label>Phone</label>
        <br>
        <input
            type="text"
            name="phone"
            value="{{ old('phone') }}"
        >

        <br><br>

        <label>Password</label>
        <br>
        <input
            type="password"
            name="password"
        >

        <br><br>

        <label>Confirm Password</label>
        <br>
        <input
            type="password"
            name="password_confirmation"
        >

        <br><br>

        <label>Role</label>
        <br>

        <select name="role">
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="cashier">Cashier</option>
            <option value="staff">Staff</option>
        </select>

        <br><br>

        <label>Status</label>
        <br>

        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <br><br>

        <button type="submit">
            Create User
        </button>

    </form>

    <br>

    <a href="{{ route('users.index') }}">
        Back to Users
    </a>

</body>
</html>