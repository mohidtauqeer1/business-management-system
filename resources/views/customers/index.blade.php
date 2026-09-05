<!DOCTYPE html>
<html>
<head>
    <title>Customers</title>
</head>
<body>

<h1>Customers</h1>

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

<a href="{{ route('customers.create') }}">
    Add Customer
</a>

<hr>

<form method="GET" action="{{ route('customers.index') }}">

    <input
        type="text"
        name="search"
        placeholder="Search customer..."
        value="{{ request('search') }}"
    >

    <button type="submit">
        Search
    </button>

    <a href="{{ route('customers.index') }}">
        Clear
    </a>

</form>

<br>

<table border="1" cellpadding="10">

    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Credit Balance</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        @forelse($customers as $customer)

            <tr>
                <td>{{ $customer->id }}</td>

                <td>{{ $customer->name }}</td>

                <td>{{ $customer->phone ?? '-' }}</td>

                <td>{{ $customer->email ?? '-' }}</td>

                <td>
                    {{ number_format($customer->credit_balance, 2) }}
                </td>

                <td>

                    <a href="{{ route('customers.show', $customer) }}">
                        View
                    </a>

                    |

                    <a href="{{ route('customers.edit', $customer) }}">
                        Edit
                    </a>

                    |

                    <form
                        method="POST"
                        action="{{ route('customers.destroy', $customer) }}"
                        style="display:inline;"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            onclick="return confirm('Delete this customer?')"
                        >
                            Delete
                        </button>
                    </form>

                </td>
            </tr>

        @empty

            <tr>
                <td colspan="6">
                    No customers found.
                </td>
            </tr>

        @endforelse

    </tbody>

</table>

<br>

{{ $customers->links() }}

</body>
</html>