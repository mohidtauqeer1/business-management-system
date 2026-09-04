<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>

<body>

<h1>Login</h1>

@if(session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif

@if($errors->any())
    <ul style="color: red;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('login') }}">

    @csrf

    <div>
        <label>Email</label>
        <br>

        <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
        >
    </div>

    <br>

    <div>
        <label>Password</label>
        <br>

        <input
            type="password"
            name="password"
            required
        >
    </div>

    <br>

    <button type="submit">
        Login
    </button>

</form>

</body>
</html>