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
      @if($user->role !== 'admin')
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Role</label>
        <select name="role" required style="width:100%;">
            <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option>
            <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
        </select>
        @error('role')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      @endif
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
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('As a {{ ucfirst($user->role) }}, deleting your account will permanently remove all your associated data. Are you sure you want to proceed?');" class="row" style="gap:12px; margin-top:12px;">
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

  <div id="confirmDialog" class="modal-backdrop">
    <div class="modal-card">
      <div class="title" style="margin-bottom:6px;">Confirm Deletion</div>
      <div class="muted" id="confirmText"></div>
      <div class="modal-actions">
        <button type="button" id="confirmNo">Cancel</button>
        <button type="button" id="confirmYes" style="background:#ef4444; border-color:#ef4444;">Delete</button>
      </div>
    </div>
  </div>
</div>
<script>
  (function(){
    const form = document.getElementById('deleteAccountForm');
    const dialog = document.getElementById('confirmDialog');
    const yesBtn = document.getElementById('confirmYes');
    const noBtn = document.getElementById('confirmNo');
    const text = document.getElementById('confirmText');
    const userRole = "{{ ucfirst($user->role) }}";

    if(form){
      form.addEventListener('submit', function(e){
        e.preventDefault();
        text.textContent = 'As a ' + userRole + ', deleting your account will permanently remove all your associated data. Are you sure you want to proceed?';
        dialog.style.display = 'flex';
      });
    }

    function close(){ dialog.style.display = 'none'; }
    noBtn.addEventListener('click', close);
    dialog.addEventListener('click', function(e){ if(e.target === dialog) close(); });
    yesBtn.addEventListener('click', function(){
      form.submit();
    });
  })();
</script>
@endsection

