@extends('layouts.app')

@section('title', 'LearnTech — Courses')

@section('content')
    <form method="GET" class="row" style="margin-bottom:16px;">
        <input type="text" name="q" placeholder="Search courses" value="{{ $search }}" />
        <select name="category">
            <option value="">All categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat }}" @selected($activeCategory === $cat)>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
    </form>

    <div class="grid">
        @forelse ($courses as $course)
            <div class="card">

                <h2 style="margin:0 0 6px; font-size:18px;">{{ $course->title }}</h2>
                <div class="muted">Category: {{ ucfirst($course->category) }} · Rating: {{ number_format($course->rating, 1) }} · Views: {{ $course->views_count }} · Enrollments: {{ $course->enrollments_count }}</div>
                @if ($course->description)
                    <p style="margin:8px 0;">{{ Str::limit($course->description, 140) }}</p>
                @endif
                @php($tags = $course->tags_array)
                @if(!empty($tags))
                    <div class="row">
                        @foreach ($tags as $t)
                            <span class="badge" style="color:#a7f3d0;border-color:#10b98144;">#{{ $t }}</span>
                        @endforeach
                    </div>
                @endif
                <div class="row" style="margin-top:10px;">
                    <a class="badge" href="{{ route('courses.show', $course->slug) }}">View details</a>
                </div>
            </div>
        @empty
            <p class="muted">No courses found.</p>
        @endforelse
    </div>

    @if ($courses->hasPages())
        <div class="row" style="justify-content:center; margin: 20px 0;">
            @if ($courses->onFirstPage())
                <span class="badge">Prev</span>
            @else
                <a class="badge" href="{{ $courses->previousPageUrl() }}">Prev</a>
            @endif

            <span class="badge" style="background: var(--accent); border-color: var(--accent); color:#fff;">Page {{ $courses->currentPage() }} / {{ $courses->lastPage() }}</span>

            @if ($courses->hasMorePages())
                <a class="badge" href="{{ $courses->nextPageUrl() }}">Next</a>
            @else
                <span class="badge">Next</span>
            @endif
        </div>
    @endif
@endsection