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
        <label style="width:100%;align-items:center;display:flex;gap:8px;">
            <input type="checkbox" id="register_as_admin" name="register_as_admin" />
            <span>Register as admin</span>
        </label>
        <p class="muted" style="font-size:12px;">If selected, you’ll be redirected to the admin registration page.</p>
        <div class="row" style="justify-content:space-between;width:100%;">
            <button type="submit">Register</button>
            <a href="{{ route('login') }}">Already have an account? Login</a>
        </div>
    </form>
    <script>
        (function(){
            const form = document.getElementById('registerForm');
            if(!form) return;
            form.addEventListener('submit', function(e){
                const admin = document.getElementById('register_as_admin');
                if(admin && admin.checked){
                    e.preventDefault();
                    window.location.href = "{{ route('admin.register') }}";
                }
            });
        })();
    </script>
</div>
@endsection