<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'LearnHub')</title>
    <style>
        /* LearnTech light theme */
        :root { --bg:#10cfe3; --bg-soft:#10cfe3; --panel:#ffffff; --panel-soft:#ffffff; --border:#e5e7eb; --muted:#6b7280; --text:#111827; --accent:#06b6d4; --accent-2:#10b981; }
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--text);
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        header {
            position: sticky; top: 0; z-index: 50;
            background: #fff; color: var(--text); padding: 16px 24px; border-bottom: 1px solid var(--border);
        }
        header .brand a { color: var(--text); text-decoration: none; font-weight: 700; letter-spacing: 0.2px; }
        header nav a { color: var(--text); }
        .container { max-width: 1080px; margin: 0 auto; padding: 24px; }
        .card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px; padding: 16px;
            box-shadow: 0 8px 24px rgba(2, 8, 23, 0.45);
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }
        .card:hover { transform: translateY(-2px); border-color: #334155; box-shadow: 0 10px 28px rgba(2, 8, 23, 0.6); }
        .row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .badge {
            background: #f3f4f6; color: var(--text);
            border: 1px solid var(--border); padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 500;
        }
        .pill { display: inline-block; padding: 6px 10px; border-radius: 999px; background: #f3f4f6; color: var(--text); font-size: 12px; }
        .muted { color: var(--muted); }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        @media (max-width: 700px) { .grid { grid-template-columns: 1fr; } }
        footer { text-align: center; font-size: 12px; color: var(--muted); padding: 24px 0; }
        input, select, button {
            padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--panel-soft); color: var(--text);
        }
        input::placeholder, select { color: #cbd5e1; }
        button { background: var(--accent); border-color: var(--accent); color: #fff; cursor: pointer; font-weight: 600; }
        button:hover { filter: brightness(1.08); }
        .title { font-weight: 700; }
        .assistant-button { position: fixed; bottom: 20px; right: 20px; background: var(--accent-2); border-color: var(--accent-2); color:#fff; border-radius:999px; padding:10px 14px; z-index:1000; }
         .assistant-panel { position: fixed; bottom: 80px; right: 20px; width: 340px; max-height: 60vh; display: none; flex-direction: column; gap: 8px; background: var(--panel); border: 1px solid var(--border); border-radius: 12px; padding: 12px; box-shadow: 0 14px 40px rgba(2,8,23,0.35); z-index:1000; }
         .assistant-header { font-weight: 600; }
         .assistant-input { flex: 1; border:1px solid var(--border); border-radius:10px; padding:8px 10px; background: var(--panel-soft); color: var(--text); }
         .assistant-results { display: grid; grid-template-columns: 1fr; gap: 8px; overflow: auto; }
         .assistant-card { border:1px solid var(--border); border-radius:10px; padding:10px; background: var(--panel-soft); }
    </style>
    @stack('styles')
    @yield('head')
</head>
<body>
<header>
    <div class="container" style="padding:0;">
        <div class="row" style="justify-content: space-between;">
            <div class="brand"><a href="/">LearnHub</a></div>
            <nav class="row">
                <a href="/">Home</a>
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    @if (auth()->user()->is_admin)
                        <a href="/admin/courses">Admin</a>
                        <a href="{{ route('admin.users.index') }}">Users</a>
                        <a href="{{ route('admin.recommendations.settings') }}">Recommendations</a>
                        <a href="{{ route('admin.recommendations.featured.index') }}">Featured</a>
                        <a href="{{ route('admin.recommendations.logs.index') }}">Logs</a>
                    @endif
                    <span class="pill">Hi, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" style="background:#ef4444;border-color:#ef4444;">Logout</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endguest
            </nav>
        </div>
    </div>
</header>
<main class="container">
    @if (session('status'))
        <div class="card" style="border-color:#10b981;background:#ecfdf5;color:#065f46;margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="card" style="border-color:#ef4444;background:#fee2e2;color:#7f1d1d;margin-bottom:16px;">
            <div><strong>There were some problems:</strong></div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
<footer>
    © {{ date('Y') }} LearnHub
</footer>
<div id="assistant-panel" class="assistant-panel" aria-live="polite" style="display:none;">
    <div class="assistant-header">Hi! 👋 I can help you find your next course.</div>
    <div class="assistant-suggestions row" style="gap:6px;">
        <button class="badge" data-suggest="Beginner">Beginner</button>
        <button class="badge" data-suggest="Trending">Trending</button>
        <button class="badge" data-suggest="Laravel">Laravel</button>
        <button class="badge" data-suggest="AI">AI</button>
    </div>
    <div class="row" style="margin-top:8px; gap:6px;">
        <input id="assistant-input" class="assistant-input" type="text" placeholder="Ask me something like: Beginner Python courses" style="flex:1;"/>
        <button id="assistant-send" class="badge">Send</button>
    </div>
    <div id="assistant-results" class="assistant-results"></div>
</div>
<button id="assistant-toggle" class="assistant-button">Ask AI</button>

<script>window.__USER_ID__ = {!! json_encode(Auth::id()) !!};</script>
<script>
(function(){
  const panel = document.getElementById('assistant-panel');
  const toggle = document.getElementById('assistant-toggle');
  const input = document.getElementById('assistant-input');
  const send = document.getElementById('assistant-send');
  const results = document.getElementById('assistant-results');
  const USER_ID = (typeof window.__USER_ID__ !== 'undefined' && window.__USER_ID__ !== null) ? window.__USER_ID__ : null;

  function renderCourses(courses){
    results.innerHTML = '';
    if(!Array.isArray(courses) || courses.length === 0){
      results.innerHTML = '<p class="muted">No recommendations found.</p>';
      return;
    }
    courses.forEach(c => {
      const card = document.createElement('div');
      card.className = 'assistant-card';
      const rating = (typeof c.rating === 'number') ? c.rating.toFixed(1) : (c.rating || '0.0');
      const enrollments = (typeof c.enrollments_count === 'number') ? c.enrollments_count : (c.enrollments_count || 0);
      card.innerHTML = `
        <div class="title" style="margin:0 0 6px; font-size:14px;">${c.title}</div>
        <div class="muted">${(c.category || '—')} · ⭐ ${rating} · 👥 ${enrollments}</div>
        <a class="badge" style="margin-top:8px;" href="/courses/${c.slug}">Open</a>
      `;
      results.appendChild(card);
    });
  }

  async function runQuery(q){
    results.innerHTML = '<p class="muted">Thinking…</p>';
    const lq = (q || '').toLowerCase();
    try{
      let data = null;
      if(lq.includes('trending')){
        const res = await fetch('/api/recommendations/trending/courses');
        data = await res.json();
      } else if(lq.includes('beginner')) {
        const res = await fetch('/api/recommendations/trending/courses');
        data = await res.json();
        data = Array.isArray(data) ? data.filter(c => !String(c.tags || '').toLowerCase().includes('advanced')) : data;
      } else if(lq.includes('laravel')){
        const res = await fetch('/api/recommendations/trending/courses');
        data = await res.json();
        data = Array.isArray(data) ? data.filter(c => String(c.category || '').toLowerCase().includes('laravel')) : data;
      } else if(lq.includes('ai')){
        const res = await fetch('/api/recommendations/trending/courses');
        data = await res.json();
        data = Array.isArray(data) ? data.filter(c => String(c.category || '').toLowerCase().includes('ai')) : data;
      } else {
        try {
          const url = USER_ID ? `/api/recommendations/personalized/courses?user_id=${USER_ID}` : '/api/recommendations/personalized/courses';
          const resP = await fetch(url);
          if (resP.ok) data = await resP.json();
        } catch(e){}
        if(!data){
          const resT = await fetch('/api/recommendations/trending/courses');
          data = await resT.json();
        }
      }
      renderCourses(data);
    } catch(e){
      results.innerHTML = '<p class="muted">Something went wrong. Try again.</p>';
    }
  }

  if(toggle){
    toggle.addEventListener('click', function(){
      const isOpen = panel && panel.style.display !== 'none' && panel.style.display !== '';
      if(panel){ panel.style.display = isOpen ? 'none' : 'flex'; }
    });
  }

  if(send){ send.addEventListener('click', () => runQuery(input.value)); }
  if(input){ input.addEventListener('keydown', (e) => { if(e.key === 'Enter'){ runQuery(input.value); } }); }
  document.querySelectorAll('.assistant-suggestions .badge').forEach(btn => {
    btn.addEventListener('click', () => {
      input.value = btn.dataset.suggest;
      runQuery(input.value);
    });
  });
})();
</script>

@stack('scripts')
</body>
</html>
