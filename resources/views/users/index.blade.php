<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
</head>
<body>

    <h1>Users</h1>

    @if(session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if(session('error'))
        <p style="color: red;">
            {{ session('error') }}
        </p>
    @endif

    <a href="{{ route('users.create') }}">Create User</a>

    <br><br>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @forelse($users as $user)

                <tr>
                    <td>{{ $user->id }}</td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>{{ ucfirst($user->role) }}</td>

                    <td>{{ $user->phone ?? '-' }}</td>

                    <td>{{ ucfirst($user->status) }}</td>

                    <td>
                        <a href="{{ route('users.show', $user) }}">
                            View
                        </a>

                        |

                        <a href="{{ route('users.edit', $user) }}">
                            Edit
                        </a>

                        |

                        <form
                            action="{{ route('users.destroy', $user) }}"
                            method="POST"
                            style="display:inline;"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="7">
                        No users found.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    <br>

    {{ $users->links() }}

    <br>

    <a href="{{ route('dashboard') }}">
        Back to Dashboard
    </a>

</body>
</html>