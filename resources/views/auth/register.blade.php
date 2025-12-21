@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="card" style="max-width:520px;margin:0 auto;">
    <h2 style="margin-top:0;">Create Account</h2>
    <form method="POST" action="{{ route('register.post') }}" class="row" style="gap:8px;" id="registerForm">
        @csrf
        <label style="width:100%;">
            <div class="muted">Name</div>
            <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" style="width:100%;" />
        </label>
        <label style="width:100%;">
            <div class="muted">Email</div>
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" style="width:100%;" />
        </label>
        <label style="width:100%;">
            <div class="muted">Password</div>
            <input type="password" name="password" required autocomplete="new-password" style="width:100%;" />
        </label>
        <label style="width:100%;">
            <div class="muted">Confirm Password</div>
            <input type="password" name="password_confirmation" required autocomplete="new-password" style="width:100%;" />
        </label>
        <label style="width:100%;">
            <div class="muted">Role</div>
            <select name="role" required style="width:100%;">
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
            </select>
        </label>
        <div class="row" style="justify-content:space-between;width:100%;">
            <button type="submit">Register</button>
            <div style="display:flex; flex-direction:column; align-items:flex-end;">
                <a href="{{ route('login') }}" style="font-size:14px;">Already have an account? Login</a>
                <a href="{{ route('admin.register') }}" style="font-size:12px; color:var(--muted); margin-top:4px;">Register as Admin</a>
            </div>
        </div>
    </form>
</div>
@endsection