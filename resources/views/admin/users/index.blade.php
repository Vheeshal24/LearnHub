@extends('layouts.app')

@section('title', 'Manage Users')

@section('head')
<style>
  .users-header { display:flex; align-items:center; justify-content:space-between; gap:10px; }
  .stats { display:flex; gap:10px; flex-wrap:wrap; }
  .stat-card { background: var(--panel); border:1px solid var(--border); border-radius:12px; padding:12px 14px; box-shadow: 0 8px 24px rgba(2, 8, 23, 0.45); }
  .stat-title { font-size:12px; color: var(--muted); }
  .stat-value { font-size:18px; font-weight:700; }
  .filter-row { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; }
  .users-table { width:100%; border-collapse:collapse; }
  .users-table th, .users-table td { padding:12px 10px; border-top:1px solid var(--border); }
  .users-table thead th { position:sticky; top:0; background:#f8fafc; border-bottom:1px solid var(--border); z-index:1; }
  .users-table tbody tr:hover { background:#f9fafb; }
  .actions { display:flex; gap:6px; justify-content:flex-end; }
  .modal-backdrop { position:fixed; inset:0; background:rgba(2, 8, 23, 0.45); display:none; align-items:center; justify-content:center; z-index:1000; }
  .modal-card { background: var(--panel); border:1px solid var(--border); border-radius:12px; padding:16px; max-width:420px; width:calc(100% - 32px); box-shadow: 0 8px 24px rgba(2, 8, 23, 0.55); }
  .modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
  .field { margin-bottom:10px; }
  .field label { display:block; margin-bottom:6px; color:var(--muted); }
  .field input, .field select { width:100%; }
</style>
@endsection

@section('content')
<div class="container py-4" style="padding:0;">
  <div class="users-header" style="margin-bottom:12px;">
    <h1 class="h4" style="margin:0;">Manage Users</h1>
    <button type="button" id="openCreateUser" class="badge" style="background:var(--accent);border-color:var(--accent);color:#fff;">Create User</button>
  </div>

  <div class="stats" style="margin-bottom:12px;">
    <div class="stat-card">
      <div class="stat-title">Total Users</div>
      <div class="stat-value" id="statsTotalUsers">...</div>
    </div>
    <div class="stat-card">
      <div class="stat-title">Admins</div>
      <div class="stat-value" id="statsAdmins">...</div>
    </div>
    <div class="stat-card">
      <div class="stat-title">Joined (7 days)</div>
      <div class="stat-value" id="statsNew7">...</div>
    </div>
  </div>

  <div class="row" style="justify-content:flex-start; margin-bottom:8px; gap:12px;">
    <span class="muted" id="apiStatusStats" style="font-size:12px;">Stats: —</span>
    <span class="muted" id="apiStatusUsers" style="font-size:12px;">Users: —</span>
  </div>
  <div id="apiErrorBanner" class="card" style="border-color:#ef4444;background:#fee2e2;color:#7f1d1d;margin-bottom:12px; display:none;">
    <div id="apiErrorText"></div>
  </div>
  <div id="apiSuccessBanner" class="card" style="border-color:#10b981;background:#ecfdf5;color:#065f46;margin-bottom:12px; display:none;">
    <div id="apiSuccessText"></div>
  </div>

  <div class="card" style="margin-bottom:12px;">
    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-row" id="usersFilterForm">
      <div style="flex:1; min-width:240px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Search</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Name or email" style="width:100%;" />
      </div>
      <div style="min-width:180px;">
        <label class="muted" style="display:block; margin-bottom:6px;">Role</label>
        <select name="role" style="width:100%;">
          <option value="" {{ $role === null ? 'selected' : '' }}>All</option>
          <option value="student" {{ $role === 'student' ? 'selected' : '' }}>Students</option>
          <option value="teacher" {{ $role === 'teacher' ? 'selected' : '' }}>Teachers</option>
          <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admins</option>
        </select>
      </div>
      <div>
        <button type="submit">Filter</button>
      </div>
    </form>
  </div>

  <div class="card" style="padding:0;">
    <div style="overflow:auto;">
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody id="usersTbody">
          <tr>
            <td colspan="6" class="muted" style="text-align:center; padding:20px;">Loading…</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="usersPager" style="border-top:1px solid var(--border); padding:12px;"></div>
  </div>
</div>
<div id="confirmDialog" class="modal-backdrop">
  <div class="modal-card">
    <div class="title" style="margin-bottom:6px;">Confirm Deletion</div>
    <div class="muted" id="confirmText">Delete this user? This cannot be undone.</div>
    <div class="modal-actions">
      <button type="button" id="confirmNo">Cancel</button>
      <button type="button" id="confirmYes" style="background:#ef4444; border-color:#ef4444;">Delete</button>
    </div>
  </div>
</div>
<div id="userFormDialog" class="modal-backdrop">
  <div class="modal-card">
    <div class="title" id="userFormTitle" style="margin-bottom:6px;">Create User</div>
    <form id="userForm" class="row" style="gap:8px;">
      <input type="hidden" name="mode" value="create" />
      <input type="hidden" name="user_id" value="" />
      <div class="field" style="width:100%;">
        <label for="uf_name">Name</label>
        <input id="uf_name" name="name" type="text" required />
      </div>
      <div class="field" style="width:100%;">
        <label for="uf_email">Email</label>
        <input id="uf_email" name="email" type="email" required />
      </div>
      <div class="field" style="width:100%;">
        <label for="uf_password">Password</label>
        <input id="uf_password" name="password" type="password" />
      </div>
      <div class="field" style="width:100%;">
        <label for="uf_role">Role</label>
        <select id="uf_role" name="role" required>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="admin">Admin</option>
        </select>
      </div>
    </form>
    <div class="modal-actions">
      <button type="button" id="userFormCancel">Cancel</button>
      <button type="button" id="userFormSave" style="background:var(--accent); border-color:var(--accent); color:#fff;">Save</button>
    </div>
  </div>
  </div>
<script>
  (function(){
    const form = document.getElementById('usersFilterForm');
    const tbody = document.getElementById('usersTbody');
    const pager = document.getElementById('usersPager');
    const USER_ID = (typeof window.__USER_ID__ !== 'undefined' && window.__USER_ID__ !== null) ? window.__USER_ID__ : null;
    let currentPage = 1;
    function setErrorBanner(text){
      const b = document.getElementById('apiErrorBanner');
      const t = document.getElementById('apiErrorText');
      if(!b || !t) return;
      if(text && String(text).trim() !== ''){
        t.textContent = text;
        b.style.display = 'block';
      } else {
        t.textContent = '';
        b.style.display = 'none';
      }
    }
    function setSuccessBanner(text){
      const b = document.getElementById('apiSuccessBanner');
      const t = document.getElementById('apiSuccessText');
      if(!b || !t) return;
      if(text && String(text).trim() !== ''){
        t.textContent = text;
        b.style.display = 'block';
      } else {
        t.textContent = '';
        b.style.display = 'none';
      }
    }
    async function loadStats(){
      try {
        document.getElementById('apiStatusStats').textContent = 'Stats: Loading…';
        const res = await fetch('/api/admin/users/stats', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const statusText = 'Stats: ' + res.status + ' ' + (res.statusText || '');
        if(!res.ok){
          document.getElementById('apiStatusStats').textContent = statusText;
          let msg = '';
          try { const j = await res.json(); if(j && j.message) msg = String(j.message); } catch(_){ try { msg = await res.text(); } catch(__){} }
          setErrorBanner(statusText + (msg ? ' · ' + msg : ''));
          throw new Error('Failed');
        }
        const s = await res.json();
        document.getElementById('statsTotalUsers').textContent = (s && typeof s.total !== 'undefined') ? s.total : '0';
        document.getElementById('statsAdmins').textContent = (s && typeof s.admins !== 'undefined') ? s.admins : '0';
        document.getElementById('statsNew7').textContent = (s && typeof s.new_last_7 !== 'undefined') ? s.new_last_7 : '0';
        document.getElementById('apiStatusStats').textContent = 'Stats: OK';
        setErrorBanner('');
      } catch(_) {
        document.getElementById('statsTotalUsers').textContent = '—';
        document.getElementById('statsAdmins').textContent = '—';
        document.getElementById('statsNew7').textContent = '—';
        document.getElementById('apiStatusStats').textContent = 'Stats: Network error';
        setErrorBanner('Stats: Network error');
      }
    }

    async function loadUsers(page){
      currentPage = page || 1;
      const q = (form.querySelector('input[name="q"]').value || '').trim();
      const role = form.querySelector('select[name="role"]').value || '';
      const params = new URLSearchParams();
      if(q) params.set('q', q);
      if(role) params.set('role', role);
      params.set('per_page', '15');
      params.set('page', String(currentPage));
      tbody.innerHTML = '<tr><td colspan="6" class="muted" style="text-align:center; padding:20px;">Loading…</td></tr>';
      try {
        document.getElementById('apiStatusUsers').textContent = 'Users: Loading…';
        const res = await fetch('/api/admin/users?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        const statusText = 'Users: ' + res.status + ' ' + (res.statusText || '');
        if(!res.ok){
          document.getElementById('apiStatusUsers').textContent = statusText;
          let msg = '';
          try { const j = await res.json(); if(j && j.message) msg = String(j.message); } catch(_){ try { msg = await res.text(); } catch(__){} }
          setErrorBanner(statusText + (msg ? ' · ' + msg : ''));
          throw new Error('Failed');
        }
        const data = await res.json();
        const rows = Array.isArray(data.data) ? data.data : [];
        renderRows(rows);
        renderPager(data.meta);
        document.getElementById('apiStatusUsers').textContent = 'Users: OK';
        setErrorBanner('');
      } catch(_) {
        tbody.innerHTML = '<tr><td colspan="6" class="muted" style="text-align:center; padding:20px;">Failed to load.</td></tr>';
        pager.innerHTML = '';
        document.getElementById('apiStatusUsers').textContent = 'Users: Network error';
        setErrorBanner('Users: Network error');
      }
    }

    function renderRows(rows){
      if(!rows.length){
        tbody.innerHTML = '<tr><td colspan="6" class="muted" style="text-align:center; padding:20px;">No users found.</td></tr>';
        return;
      }
      tbody.innerHTML = rows.map(function(u){
        const r = (u.role || (u.is_admin ? 'admin' : 'student'));
        const isAdmin = r === 'admin';
        const verified = !!u.email_verified_at;
        const created = (u.created_at || '').slice(0, 10);
        const canDelete = USER_ID ? (String(USER_ID) !== String(u.id)) : true;
        const roleBadge = (r === 'admin')
          ? '<span class="badge" style="background:#ecfdf5;border-color:#10b981;color:#065f46;">Admin</span>'
          : (r === 'teacher')
            ? '<span class="badge" style="background:#eef2ff;border-color:#c7d2fe;color:#3730a3;">Teacher</span>'
            : '<span class="badge" style="background:#f1f5f9;border-color:#cbd5e1;color:#475569;">Student</span>';
        const deleteBtn = canDelete
          ? '<button type="button" class="badge js-delete-user" data-user-id="'+u.id+'" data-user-name="'+(u.name || '')+'" data-user-role="'+r+'" style="background:#fee2e2;border-color:#fecaca;color:#7f1d1d;">Delete</button>'
          : '';
        return (
          '<tr>' +
            '<td class="muted">'+u.id+'</td>' +
            '<td>' +
              '<div class="title" style="font-weight:600;">'+(u.name || '')+'</div>' +
              '<div class="muted" style="font-size:12px;">' + (verified ? 'Verified' : 'Unverified') + '</div>' +
            '</td>' +
            '<td>'+(u.email || '')+'</td>' +
            '<td>'+roleBadge+'</td>' +
            '<td class="muted">'+created+'</td>' +
            '<td>' +
              '<div class="actions">' +
                '<button type="button" class="badge js-edit-user" data-user-id="'+u.id+'" data-user-name="'+(u.name || '')+'" data-user-email="'+(u.email || '')+'" data-user-role="'+r+'" style="background:#e0f2fe;border-color:#93c5fd;color:#1e40af;">Edit</button>' +
                deleteBtn +
              '</div>' +
            '</td>' +
          '</tr>'
        );
      }).join('');
      bindDelete();
      bindEdit();
    }

    const confirmDialog = document.getElementById('confirmDialog');
    const confirmText = document.getElementById('confirmText');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    let pendingDeleteId = null;
    const userFormDialog = document.getElementById('userFormDialog');
    const userFormTitle = document.getElementById('userFormTitle');
    const userForm = document.getElementById('userForm');
    const userFormCancel = document.getElementById('userFormCancel');
    const userFormSave = document.getElementById('userFormSave');
    const openCreateUser = document.getElementById('openCreateUser');

    function openDeleteConfirm(id, name, role){
      pendingDeleteId = id;
      const roleText = role ? ' (' + String(role).charAt(0).toUpperCase() + String(role).slice(1) + ')' : '';
      confirmText.textContent = 'Delete ' + (name || 'this user') + roleText + '? This cannot be undone.';
      confirmDialog.style.display = 'flex';
    }

    function closeDeleteConfirm(){
      pendingDeleteId = null;
      confirmDialog.style.display = 'none';
    }

    confirmNo.addEventListener('click', closeDeleteConfirm);
    confirmDialog.addEventListener('click', function(e){ if(e.target === confirmDialog) closeDeleteConfirm(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeDeleteConfirm(); });

    confirmYes.addEventListener('click', async function(){
      if(!pendingDeleteId) return;
      try {
        const res = await fetch('/api/admin/users/' + pendingDeleteId, { method: 'DELETE', credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
        if(!res.ok){ alert('Failed to delete user'); return; }
        closeDeleteConfirm();
        setSuccessBanner('User deleted successfully');
        loadUsers(currentPage);
      } catch(_) { alert('Network error'); }
    });

    function openUserForm(mode, payload){
      userForm.reset();
      userForm.querySelector('input[name="mode"]').value = mode;
      userForm.querySelector('input[name="user_id"]').value = payload && payload.id ? payload.id : '';
      userFormTitle.textContent = (mode === 'edit') ? 'Edit User' : 'Create User';
      document.getElementById('uf_password').placeholder = (mode === 'edit') ? 'Leave blank to keep current' : '';
      document.getElementById('uf_name').value = payload && payload.name ? payload.name : '';
      document.getElementById('uf_email').value = payload && payload.email ? payload.email : '';
      document.getElementById('uf_role').value = payload && payload.role ? payload.role : 'student';
      userFormDialog.style.display = 'flex';
    }
    function closeUserForm(){
      userFormDialog.style.display = 'none';
    }
    userFormCancel.addEventListener('click', closeUserForm);
    userFormDialog.addEventListener('click', function(e){ if(e.target === userFormDialog) closeUserForm(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeUserForm(); });
    openCreateUser.addEventListener('click', function(){ openUserForm('create', null); });
    userFormSave.addEventListener('click', async function(){
      const mode = userForm.querySelector('input[name="mode"]').value;
      const id = userForm.querySelector('input[name="user_id"]').value;
      const name = document.getElementById('uf_name').value.trim();
      const email = document.getElementById('uf_email').value.trim();
      const password = document.getElementById('uf_password').value;
      const role = document.getElementById('uf_role').value;
      const body = { name, email, role };
      if(mode === 'create' || (password && password.trim() !== '')) body.password = password;
      try {
        let res;
        if(mode === 'edit' && id){
          res = await fetch('/api/admin/users/' + id, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(body)
          });
        } else {
          res = await fetch('/api/admin/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(body)
          });
        }
        if(!res.ok){
          let msg = 'Failed';
          try { const data = await res.json(); if(data && data.message) msg = data.message; } catch(_){}
          setErrorBanner(msg);
          return;
        }
        closeUserForm();
        setErrorBanner('');
        setSuccessBanner(mode === 'edit' ? 'User updated successfully' : 'User created successfully');
        loadUsers(currentPage);
      } catch(_) {
        setErrorBanner('Network error');
      }
    });

    function bindDelete(){
      document.querySelectorAll('button.js-delete-user').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = btn.getAttribute('data-user-id');
          const name = btn.getAttribute('data-user-name') || '';
          const role = btn.getAttribute('data-user-role') || '';
          openDeleteConfirm(id, name, role);
        });
      });
    }
    function bindEdit(){
      document.querySelectorAll('button.js-edit-user').forEach(function(btn){
        btn.addEventListener('click', function(){
          const id = btn.getAttribute('data-user-id');
          const name = btn.getAttribute('data-user-name') || '';
          const email = btn.getAttribute('data-user-email') || '';
          const role = btn.getAttribute('data-user-role') || 'student';
          openUserForm('edit', { id, name, email, role });
        });
      });
    }

    function renderPager(meta){
      if(!meta){ pager.innerHTML = ''; return; }
      const cp = meta.current_page || 1;
      const lp = meta.last_page || 1;
      let html = '<div class="row" style="gap:6px;">';
      html += '<button type="button" '+(cp<=1?'disabled':'')+' data-page="'+(cp-1)+'">Prev</button>';
      const start = Math.max(1, cp - 2);
      const end = Math.min(lp, cp + 2);
      for(let p=start; p<=end; p++){
        html += '<button type="button" '+(p===cp?'disabled':'')+' data-page="'+p+'">'+p+'</button>';
      }
      html += '<button type="button" '+(cp>=lp?'disabled':'')+' data-page="'+(cp+1)+'">Next</button>';
      html += '</div>';
      pager.innerHTML = html;
      pager.querySelectorAll('button[data-page]').forEach(function(b){
        b.addEventListener('click', function(){
          const p = parseInt(b.getAttribute('data-page'), 10);
          if(!isNaN(p)) loadUsers(p);
        });
      });
    }

    form.addEventListener('submit', function(e){ e.preventDefault(); setSuccessBanner(''); loadUsers(1); });
    document.addEventListener('DOMContentLoaded', function(){ loadStats(); loadUsers(1); });
  })();
</script>
@endsection
