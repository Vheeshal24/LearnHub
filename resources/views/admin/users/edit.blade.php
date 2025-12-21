@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Edit User</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update', $user) }}" class="row g-3" id="editUserForm">
        @csrf
        @method('PUT')
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
          @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
          @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Password (leave blank to keep)</label>
          <input type="password" name="password" class="form-control" placeholder="New password">
          @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Role</label>
          <select name="role" class="form-control">
            <option value="student" {{ old('role', $user->role ?? ($user->is_admin ? 'admin' : 'student')) === 'student' ? 'selected' : '' }}>Student</option>
            <option value="teacher" {{ old('role', $user->role ?? ($user->is_admin ? 'admin' : 'student')) === 'teacher' ? 'selected' : '' }}>Teacher</option>
            <option value="admin" {{ old('role', $user->role ?? ($user->is_admin ? 'admin' : 'student')) === 'admin' ? 'selected' : '' }}>Admin</option>
          </select>
          @error('role')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  (function(){
    const form = document.getElementById('editUserForm');
    if(!form) return;
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const name = form.querySelector('input[name="name"]').value.trim();
      const email = form.querySelector('input[name="email"]').value.trim();
      const password = form.querySelector('input[name="password"]').value;
      const role = form.querySelector('select[name="role"]').value;
      const payload = { name, email, role };
      if(password && password.length > 0) payload.password = password;
      try {
        const res = await fetch('/api/admin/users/{{ $user->id }}', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify(payload)
        });
        if(!res.ok){
          let msg = 'Failed to update user';
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
