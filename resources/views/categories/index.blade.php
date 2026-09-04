<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
</head>
<body>

<h1>Categories</h1>

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

<a href="{{ route('categories.create') }}">
    Create Category
</a>

<br><br>

<table border="1" cellpadding="10">

    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Parent</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

    @forelse($categories as $category)

        <tr>

            <td>{{ $category->id }}</td>

            <td>{{ $category->name }}</td>

            <td>{{ $category->description ?? '-' }}</td>

            <td>
                {{ $category->parent?->name ?? 'Root Category' }}
            </td>

            <td>

                <a href="{{ route('categories.show', $category) }}">
                    View
                </a>

                |

                <a href="{{ route('categories.edit', $category) }}">
                    Edit
                </a>

                |

                <form
                    action="{{ route('categories.destroy', $category) }}"
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
            <td colspan="5">
                No categories found.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

<br>

{{ $categories->links() }}

<br>

<a href="{{ route('dashboard') }}">
    Back to Dashboard
</a>

</body>
</html>