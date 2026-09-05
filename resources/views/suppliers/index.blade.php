<!DOCTYPE html>
<html>
<head>
    <title>Suppliers</title>
</head>
<body>

<h1>Suppliers</h1>

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

<a href="{{ route('suppliers.create') }}">
    Add Supplier
</a>

<hr>

<form method="GET" action="{{ route('suppliers.index') }}">

    <input
        type="text"
        name="search"
        placeholder="Search supplier..."
        value="{{ request('search') }}"
    >

    <button type="submit">
        Search
    </button>

    <a href="{{ route('suppliers.index') }}">
        Clear
    </a>

</form>

<br>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact Person</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        @forelse($suppliers as $supplier)

            <tr>
                <td>{{ $supplier->id }}</td>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->contact_person ?? '-' }}</td>
                <td>{{ $supplier->phone ?? '-' }}</td>
                <td>{{ $supplier->email ?? '-' }}</td>

                <td>
                    <a href="{{ route('suppliers.show', $supplier) }}">
                        View
                    </a>

                    |

                    <a href="{{ route('suppliers.edit', $supplier) }}">
                        Edit
                    </a>

                    |

                    <form
                        method="POST"
                        action="{{ route('suppliers.destroy', $supplier) }}"
                        style="display:inline;"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            onclick="return confirm('Delete this supplier?')"
                        >
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

        @empty

            <tr>
                <td colspan="6">
                    No suppliers found.
                </td>
            </tr>

        @endforelse

    </tbody>
</table>

<br>

{{ $suppliers->links() }}

</body>
</html>