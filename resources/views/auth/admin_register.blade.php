@extends('layouts.app')

@section('title', 'Admin Registration')

@section('content')
<div class="container" style="max-width:640px;">
  <h1 class="h4" style="margin:0 0 12px; font-size:24px;">Create Admin Account</h1>

  <div class="card">
    @if ($errors->any())
        <div class="card" style="border-color:#ef4444;background:#fee2e2;color:#7f1d1d;">
            <ul style="margin:0; padding-left:16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.register.submit') }}" class="row" style="gap:12px;">
      @csrf
      <div style="width:100%;">
        <label class="muted" style="display:block; margin-bottom:6px;" for="name">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required style="width:100%;" />
      </div>
      <div style="width:100%;">
        <label class="muted" style="display:block; margin-bottom:6px;" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required style="width:100%;" />
      </div>
      <div style="width:100%;">
        <label class="muted" style="display:block; margin-bottom:6px;" for="password">Password</label>
        <input id="password" name="password" type="password" required style="width:100%;" />
      </div>
      <div style="width:100%;">
        <label class="muted" style="display:block; margin-bottom:6px;" for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required style="width:100%;" />
      </div>
      <div class="row" style="justify-content:space-between; width:100%;">
        <button type="submit">Register as Admin</button>
        <a href="{{ route('login') }}" class="badge">Back to Login</a>
      </div>
    </form>
  </div>
</div>
@endsection
