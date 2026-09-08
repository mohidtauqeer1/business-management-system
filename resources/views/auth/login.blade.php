@extends('layouts.auth')

@section('title', 'Login')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            <span class="alert-icon">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <span class="alert-icon">⚠️</span>
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required
                autofocus
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary w-100" style="margin-top:8px;padding:11px;">
            Sign In
        </button>

    </form>

@endsection