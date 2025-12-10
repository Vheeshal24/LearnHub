@extends('layouts.app')

@section('title', 'Admin: Courses')

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <h1 style="margin:0;">Admin / Courses</h1>
    <a href="{{ route('admin.courses.create') }}" class="pill" style="background: var(--accent); border-color: var(--accent); color:#fff;">+ Create Course</a>
</div>

<form method="GET" action="{{ route('admin.courses.index') }}" class="card" style="margin-bottom:16px;">
    <div class="row" style="gap:8px;">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search by title, description, or tags" style="flex:1;"/>
        <button type="submit">Search</button>
    </div>
</form>

@if($courses->count() === 0)
    <div class="card">No courses found. Create one to get started.</div>
@else
    <div class="grid">
        @foreach ($courses as $course)
            <div class="card">
                <div class="row" style="justify-content: space-between;">
                    <div>
                        <div style="font-weight:600; font-size:18px;">{{ $course->title }}</div>
                        <div class="muted">Category: {{ $course->category ?? '—' }}</div>
                        @if(!empty($course->tags))
                            <div class="row" style="gap:6px; margin-top:6px;">
                                @foreach($course->tags_array as $tag)
                                    <span class="pill">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="row" style="gap:8px;">
                        <a href="{{ route('admin.courses.edit', $course->slug) }}" class="pill">Edit</a>
                        <a href="{{ route('admin.lessons.index', $course->slug) }}" class="pill">Lessons</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course->slug) }}" onsubmit="return confirm('Delete this course?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pill" style="background:#ef4444;border-color:#ef4444;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top:16px;">
        {{ $courses->links() }}
    </div>
@endif
@endsection