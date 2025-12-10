@extends('layouts.app')

@section('title', 'Admin: Create Course')

@section('content')
<h1>Admin / Create Course</h1>

<form method="POST" action="{{ route('admin.courses.store') }}" class="card" style="margin-top:12px;">
    @csrf
    <div class="row" style="gap:12px;">
        <div style="flex:1;">
            <label>Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required />
        </div>
        <div style="width:260px;">
            <label>Category</label>
            <input type="text" name="category" value="{{ old('category') }}" required />
        </div>
    </div>

    <div style="margin-top:12px;">
        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%;">{{ old('description') }}</textarea>
    </div>

    <div class="row" style="gap:12px; margin-top:12px;">
        <div style="flex:1;">
            <label>Tags (comma-separated)</label>
            <input type="text" name="tags" value="{{ old('tags') }}" />
        </div>
        <div style="width:260px;">
            <label>Published At</label>
            <input type="date" name="published_at" value="{{ old('published_at') }}" />
        </div>
    </div>

    <div class="row" style="justify-content:flex-end; gap:8px; margin-top:16px;">
        <a href="{{ route('admin.courses.index') }}" class="pill">Cancel</a>
        <button type="submit">Create Course</button>
    </div>
</form>
@endsection