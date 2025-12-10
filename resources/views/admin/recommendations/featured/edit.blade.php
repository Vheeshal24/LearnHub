@extends('layouts.app')

@section('title', 'Admin: Edit Featured Recommendation')

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <h1 style="margin:0;">Admin / Edit Featured Recommendation</h1>
    <a href="{{ route('admin.recommendations.featured.index') }}" class="pill">← Back</a>
</div>

@if(session('error'))
    <div class="card" style="border-color:#ef4444;background:#fee2e2;color:#7f1d1d;">{{ session('error') }}</div>
@endif

<div class="card">
    <form method="POST" action="{{ route('admin.recommendations.featured.update', $featured->id) }}" class="row" style="gap:12px;">
        @csrf
        @method('PUT')

        <div style="flex:2;">
            <label>Course</label>
            <input type="text" value="{{ $featured->course->title }}" disabled style="width:100%;" />
            <div class="muted">To change the course, enter ID below.</div>
            <input type="number" name="course_id" value="{{ old('course_id') }}" placeholder="New course id (optional)" />
        </div>

        <div style="flex:1;">
            <label>Priority</label>
            <input type="number" name="priority" min="0" max="100" value="{{ old('priority', $featured->priority) }}" />
        </div>

        <div style="flex:1;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" id="active" name="active" value="1" {{ old('active', $featured->active) ? 'checked' : '' }} />
            <label for="active" style="margin:0;">Active</label>
        </div>

        <div style="flex:1;">
            <label>Starts At</label>
            <input type="date" name="starts_at" value="{{ old('starts_at', optional($featured->starts_at)->format('Y-m-d')) }}" />
        </div>
        <div style="flex:1;">
            <label>Ends At</label>
            <input type="date" name="ends_at" value="{{ old('ends_at', optional($featured->ends_at)->format('Y-m-d')) }}" />
        </div>

        <div style="flex:1;">
            <label>Note</label>
            <input type="text" name="note" value="{{ old('note', $featured->note) }}" style="width:100%;" />
        </div>

        <div class="row" style="justify-content:flex-end; gap:8px;">
            <a href="{{ route('admin.recommendations.featured.index') }}" class="pill">Cancel</a>
            <button type="submit">Save</button>
        </div>
    </form>
</div>
@endsection