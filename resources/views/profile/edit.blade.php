@extends('layouts.app')

@section('title', 'Your Profile')

@section('content')
<div class="container py-4">
  <div class="row" style="justify-content:space-between; margin-bottom:12px;">
    <h1 class="h4" style="margin:0;">Your Profile</h1>
    <a href="{{ route('dashboard') }}" class="badge">Back</a>
  </div>

  <div class="card" style="margin-bottom:16px;">
    <form method="POST" action="{{ route('profile.update') }}" class="row" style="gap:12px;">
      @csrf
      @method('PUT')
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">New Password</label>
        <input type="password" name="password" placeholder="Leave blank to keep current">
        @error('password')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="width:100%; display:flex; justify-content:flex-start;">
        <button type="submit">Save Changes</button>
      </div>
    </form>
  </div>

  <div class="card" style="border-color:#ef4444;">
    <div class="row" style="justify-content: space-between;">
      <div>
        <div class="title">Delete Account</div>
        <div class="muted">This action is permanent.</div>
      </div>
    </div>
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete your account? This cannot be undone.');" class="row" style="gap:12px; margin-top:12px;">
      @csrf
      @method('DELETE')
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Confirm Password</label>
        <input type="password" name="current_password" required>
        @error('current_password')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div>
        <button type="submit" style="background:#ef4444; border-color:#ef4444;">Delete Account</button>
      </div>
    </form>
  </div>
</div>
@endsection

