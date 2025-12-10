@extends('layouts.app')

@section('title', 'Admin: Edit Course')

@section('content')
<h1>Admin / Edit Course</h1>

<form method="POST" action="{{ route('admin.courses.update', $course->slug) }}" class="card" style="margin-top:12px;">
    @csrf
    @method('PUT')
    <div class="row" style="gap:12px;">
        <div style="flex:1;">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title', $course->title) }}" />
        </div>
        <div style="width:260px;">
            <label>Category</label>
            <input type="text" name="category" value="{{ old('category', $course->category) }}" />
        </div>
    </div>

    <div style="margin-top:12px;">
        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%;">{{ old('description', $course->description) }}</textarea>
    </div>

    <div class="row" style="gap:12px; margin-top:12px;">
        <div style="flex:1;">
            <label>Tags (comma-separated)</label>
            <input type="text" name="tags" value="{{ old('tags', $course->tags) }}" />
        </div>
        <div style="width:260px;">
            <label>Published At</label>
            <input type="date" name="published_at" value="{{ old('published_at', optional($course->published_at)->format('Y-m-d')) }}" />
        </div>
    </div>

    <div class="row" style="justify-content:flex-end; gap:8px; margin-top:16px;">
        <a href="{{ route('admin.courses.index') }}" class="pill">Cancel</a>
        <button type="submit">Save Changes</button>
    </div>
</form>
@endsection