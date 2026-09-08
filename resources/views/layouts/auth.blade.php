<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — BizManager</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">🏢</div>
            <div class="auth-logo-title">BizManager</div>
            <div class="auth-logo-sub">Business & Inventory Management</div>
        </div>

        @yield('content')
    </div>
</div>

</body>
</html>
