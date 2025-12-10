@extends('layouts.app')

@section('title', 'Admin: New Featured Recommendation')

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <h1 style="margin:0;">Admin / New Featured Recommendation</h1>
    <a href="{{ route('admin.recommendations.featured.index') }}" class="pill">← Back</a>
</div>

@if(session('error'))
    <div class="card" style="border-color:#ef4444;background:#fee2e2;color:#7f1d1d;">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.recommendations.featured.store') }}" class="card" style="margin-bottom:16px;">
    @csrf
    <div class="row" style="gap:12px;">
        <div style="flex:2;">
            <label>Course</label>
            <select name="course_id" required style="width:100%;">
                <option value="">Select a course</option>
                @foreach ($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->title }} ({{ ucfirst($c->category) }})</option>
                @endforeach
            </select>
            <div class="muted">Use search below to narrow the list.</div>
        </div>
        <div style="flex:1;">
            <label>Priority</label>
            <input type="number" name="priority" min="0" max="100" value="{{ old('priority', 0) }}"/>
            <div class="muted">Higher shows first.</div>
        </div>
        <div style="flex:1;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }} />
            <label for="active" style="margin:0;">Active</label>
        </div>
    </div>

    <div class="row" style="gap:12px; margin-top:12px;">
        <div style="flex:1;">
            <label>Starts At</label>
            <input type="date" name="starts_at" value="{{ old('starts_at') }}" />
        </div>
        <div style="flex:1;">
            <label>Ends At</label>
            <input type="date" name="ends_at" value="{{ old('ends_at') }}" />
        </div>
    </div>

    <div style="margin-top:12px;">
        <label>Note</label>
        <input type="text" name="note" value="{{ old('note') }}" style="width:100%;" />
    </div>

    <div class="row" style="justify-content:flex-end; gap:8px; margin-top:16px;">
        <a href="{{ route('admin.recommendations.featured.index') }}" class="pill">Cancel</a>
        <button type="submit">Create Featured</button>
    </div>
</form>

<form method="GET" action="{{ route('admin.recommendations.featured.create') }}" class="card" style="">
    <div class="row" style="gap:8px;">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search courses to select" style="flex:1;" />
        <button type="submit">Search</button>
    </div>
</form>
@endsection