@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container py-4">
  <div class="row" style="justify-content:space-between; margin-bottom:12px;">
    <h1 class="h4" style="margin:0;">Create User</h1>
    <a href="{{ route('admin.users.index') }}" class="badge">Back</a>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('admin.users.store') }}" class="row" style="gap:12px;" id="createUserForm">
      @csrf
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="flex:1; min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Password</label>
        <input type="password" name="password" required>
        @error('password')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="min-width:220px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Role</label>
        <select name="role" id="role" style="width:100%;">
          <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
          <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
          <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        @error('role')<div class="muted" style="color:#b91c1c; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
      </div>
      <div style="width:100%; display:flex; justify-content:flex-start;">
        <button type="submit">Create</button>
      </div>
    </form>
  </div>
</div>
<script>
  (function(){
    const form = document.getElementById('createUserForm');
    if(!form) return;
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const name = form.querySelector('input[name="name"]').value.trim();
      const email = form.querySelector('input[name="email"]').value.trim();
      const password = form.querySelector('input[name="password"]').value;
      const role = form.querySelector('select[name="role"]').value;
      try {
        const res = await fetch('/api/admin/users', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ name, email, password, role })
        });
        if(!res.ok){
          let msg = 'Failed to create user';
          try { const data = await res.json(); if(data && data.message) msg = data.message; } catch(_){ }
          alert(msg);
          return;
        }
        window.location.href = "{{ route('admin.users.index') }}";
      } catch(_) {
        alert('Network error');
      }
    });
  })();
</script>
@endsection
