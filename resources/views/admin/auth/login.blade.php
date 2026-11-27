@extends('layouts.auth')
@section('title', 'Sign in')
@section('body-class', 'login')

@section('content')
<div class="ad-auth-card">
    <div class="text-center mb-4">
        <img class="ad-auth-logo mb-3" src="{{ $site->image('logo') }}" alt="">
        <h1 class="h4 mb-1">{{ config('app.name') }}</h1>
        <p class="text-secondary mb-0">Sign in to the admin console</p>
    </div>

    @if (request()->boolean('installed'))
        <div class="alert alert-success small"><i class="fa-solid fa-circle-check me-1"></i> Installation complete. Sign in with the master admin account you just created.</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" data-once>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">Email address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="username" required autofocus>
            </div>
            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
                <button class="btn btn-outline-secondary" type="button" data-toggle-password="#password" aria-label="Show or hide password"><i class="fa-solid fa-eye"></i></button>
            </div>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label small" for="remember">Keep me signed in</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2"><i class="fa-solid fa-right-to-bracket me-2"></i> Sign in</button>
    </form>

    <p class="text-center small text-secondary mt-4 mb-0"><a href="{{ url('/') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to website</a></p>
</div>
@endsection
