<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>

    <h1>Edit User</h1>

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        action="{{ route('users.update', $user) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        <label>Name</label>
        <br>

        <input
            type="text"
            name="name"
            value="{{ old('name', $user->name) }}"
        >

        <br><br>

        <label>Email</label>
        <br>

        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email) }}"
        >

        <br><br>

        <label>Phone</label>
        <br>

        <input
            type="text"
            name="phone"
            value="{{ old('phone', $user->phone) }}"
        >

        <br><br>

        <label>Role</label>
        <br>

        <select name="role">

            <option value="admin"
                {{ $user->role === 'admin' ? 'selected' : '' }}>
                Admin
            </option>

            <option value="manager"
                {{ $user->role === 'manager' ? 'selected' : '' }}>
                Manager
            </option>

            <option value="cashier"
                {{ $user->role === 'cashier' ? 'selected' : '' }}>
                Cashier
            </option>

            <option value="staff"
                {{ $user->role === 'staff' ? 'selected' : '' }}>
                Staff
            </option>

        </select>

        <br><br>

        <label>Status</label>
        <br>

        <select name="status">

            <option value="active"
                {{ $user->status === 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive"
                {{ $user->status === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        <br><br>

        <label>
            New Password
            <small>(leave empty to keep current password)</small>
        </label>

        <br>

        <input
            type="password"
            name="password"
        >

        <br><br>

        <label>Confirm New Password</label>
        <br>

        <input
            type="password"
            name="password_confirmation"
        >

        <br><br>

        <button type="submit">
            Update User
        </button>

    </form>

    <br>

    <a href="{{ route('users.index') }}">
        Back to Users
    </a>

</body>
</html>