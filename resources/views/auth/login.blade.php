@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card" style="max-width:520px;margin:0 auto;">
    <h2 style="margin-top:0;">Login</h2>
    <form method="POST" action="{{ route('login.post') }}" class="row" style="gap:8px;">
        @csrf
        <label style="width:100%;">
            <div class="muted">Email</div>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" style="width:100%;" />
        </label>
        <label style="width:100%;">
            <div class="muted">Password</div>
            <input type="password" name="password" required autocomplete="current-password" style="width:100%;" />
        </label>
        <label class="row" style="gap:6px;">
            <input type="checkbox" name="remember" value="1" />
            <span class="muted">Remember me</span>
        </label>
        <div class="row" style="justify-content:space-between;width:100%;">
            <button type="submit">Login</button>
            <a href="{{ route('register') }}">Create an account</a>
        </div>
    </form>
</div>
@endsection