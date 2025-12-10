@extends('layouts.app')

@section('title', 'Admin: Lessons for ' . $course->title)

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <div>
        <h1 style="margin:0;">Admin / Lessons</h1>
        <div class="muted">Course: <a href="{{ route('admin.courses.edit', $course->slug) }}">{{ $course->title }}</a></div>
    </div>
    <div class="row" style="gap:8px;">
        <a href="{{ route('admin.courses.index') }}" class="pill">← Back to Courses</a>
        <a href="{{ route('admin.lessons.create', $course->slug) }}" class="pill" style="background: var(--accent); border-color: var(--accent); color:#fff;">+ Create Lesson</a>
    </div>
</div>

@if($lessons->count() === 0)
    <div class="card">No lessons yet. Create the first lesson.</div>
@else
    <div class="grid">
        @foreach ($lessons as $lesson)
            <div class="card">
                <div class="row" style="justify-content: space-between;">
                    <div>
                        <div class="muted">#{{ $lesson->position }}</div>
                        <div style="font-weight:600; font-size:18px;">{{ $lesson->title }}</div>
                        @if(!empty($lesson->duration_minutes))
                            <div class="muted">Duration: {{ $lesson->duration_minutes }} min</div>
                        @endif
                        <div class="muted">Published: {{ $lesson->published ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="row" style="gap:8px;">
                        <a href="{{ route('admin.lessons.edit', [$course->slug, $lesson->slug]) }}" class="pill">Edit</a>
                        <form method="POST" action="{{ route('admin.lessons.destroy', [$course->slug, $lesson->slug]) }}" onsubmit="return confirm('Delete this lesson?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pill" style="background:#ef4444;border-color:#ef4444;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection