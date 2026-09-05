<!DOCTYPE html>
<html>
<head>
    <title>Create Supplier</title>
</head>
<body>

<h1>Create Supplier</h1>

@if($errors->any())
    <ul style="color:red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('suppliers.update', $supplier) }}">

    @csrf
    @method('PUT')

    <label>Name</label><br>
    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        required
    >

    <br><br>

    <label>Contact Person</label><br>
   <input
    type="text"
    name="contact_person"
    value="{{ old('contact_person', $supplier->contact_person) }}"
>

    <br><br>

    <label>Phone</label><br>
    <input
    type="text"
    name="phone"
    value="{{ old('phone', $supplier->phone) }}"
>

    <br><br>

    <label>Email</label><br>
   <input
    type="email"
    name="email"
    value="{{ old('email', $supplier->email) }}"
>

    <br><br>

    <label>Address</label><br>
   <textarea name="address">{{ old('address', $supplier->address) }}</textarea>

    <br><br>

    <button type="submit">
    Update Supplier
</button>

    <a href="{{ route('suppliers.index') }}">
        Cancel
    </a>

</form>

</body>
</html>