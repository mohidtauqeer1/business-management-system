<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
</head>
<body>

    <h1>User Details</h1>

    <p>
        <strong>ID:</strong>
        {{ $user->id }}
    </p>

    <p>
        <strong>Name:</strong>
        {{ $user->name }}
    </p>

    <p>
        <strong>Email:</strong>
        {{ $user->email }}
    </p>

    <p>
        <strong>Phone:</strong>
        {{ $user->phone ?? '-' }}
    </p>

    <p>
        <strong>Role:</strong>
        {{ ucfirst($user->role) }}
    </p>

    <p>
        <strong>Status:</strong>
        {{ ucfirst($user->status) }}
    </p>

    <p>
        <strong>Created:</strong>
        {{ $user->created_at }}
    </p>

    <br>

    <a href="{{ route('users.edit', $user) }}">
        Edit User
    </a>

    <br><br>

    <a href="{{ route('users.index') }}">
        Back to Users
    </a>

</body>
</html>