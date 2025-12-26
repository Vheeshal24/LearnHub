@extends('layouts.app')

@section('title', 'Admin: Edit Lesson')

@section('content')
<h1>Admin / Edit Lesson</h1>
<div class="muted">Course: <a href="{{ route('admin.lessons.index', $course->slug) }}">{{ $course->title }}</a></div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.lessons.update', [$course->slug, $lesson->slug]) }}" class="card" style="margin-top:12px;">
    @csrf
    @method('PUT')
    <div class="row" style="gap:12px;">
        <div style="flex:1;">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required />
        </div>
        <div style="width:220px;">
            <label>Position</label>
            <input type="number" name="position" value="{{ old('position', $lesson->position) }}" min="0" />
        </div>
    </div>

    <div style="margin-top:12px;">
        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%;">{{ old('description', $lesson->description) }}</textarea>
    </div>

    <div class="row" style="gap:12px; margin-top:12px;">
        <div style="flex:1;">
            <label>Content URL (YouTube/MP4/Link)</label>
            <input type="text" name="content_url" value="{{ old('content_url', $lesson->content_url) }}" />
        </div>
        <div style="margin-top:12px;">
            <label>Change Lesson Material (PDF)</label>
            <input type="file" name="material_file" accept=".pdf">
        </div>
        <div style="width:220px;">
            <label>Duration (minutes)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="0" />
        </div>
        <div style="width:220px;">
            <label>Published</label>
            <select name="published">
                <option value="0" {{ old('published', $lesson->published ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('published', $lesson->published ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
    </div>

    <div style="margin-top:12px;">
        <label>Quiz JSON (optional)</label>
        <textarea name="quiz_json" rows="6" style="width:100%;" placeholder='{"questions":[{"text":"2+2?","options":["3","4","5"],"answer":1}]}'>{!! old('quiz_json', $lesson->quiz_json) !!}</textarea>
        <div class="muted">Provide a simple JSON with fields: questions[].text, questions[].options[], questions[].answer (index).</div>
    </div>

    <div class="row" style="justify-content:flex-end; gap:8px; margin-top:16px;">
        <a href="{{ route('admin.lessons.index', $course->slug) }}" class="pill">Cancel</a>
        <button type="submit">Save Changes</button>
    </div>
</form>
@endsection