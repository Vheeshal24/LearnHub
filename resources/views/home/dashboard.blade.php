@extends('layouts.app')

@section('title', 'LearnTech — Dashboard')

@section('content')
<div class="grid">
    <div class="card">
        <h2 style="margin-top:0;">Welcome back!</h2>
        <p class="muted">Here’s your learning overview and recommended courses.</p>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Personalized For You</h3>
        @forelse($personalized as $course)
            <div class="course-item">

                <div class="details">
                    <div class="title-row">
                        <strong class="title">{{ $course->title }}</strong>
                        <span class="badge">{{ ucfirst($course->category) }}</span>
                        <span class="badge">{{ difficulty_badge($course) }}</span>
                    </div>
                    <div class="muted">⭐ {{ number_format($course->rating, 1) }} · 👥 {{ number_format($course->enrollments_count) }} · ⏱ {{ $course->lessons->sum('duration_minutes') }} min</div>
                    <a class="badge" href="{{ route('courses.show', $course->slug) }}">Open</a>
                </div>
            </div>
        @empty
            <p class="muted">No personalized recommendations yet.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Continue Learning</h3>
        @forelse($continueCourses as $course)
            <div class="course-item">

                <div class="details">
                    <div class="title-row">
                        <strong class="title">{{ $course->title }}</strong>
                        <span class="badge">{{ ucfirst($course->category) }}</span>
                        <span class="badge">{{ difficulty_badge($course) }}</span>
                    </div>
                    <div class="muted">Progress: {{ $course->activities()->where('user_id', auth()->id())->where('type','lesson_completed')->count() }} completed</div>
                    <a class="badge" href="{{ route('courses.show', $course->slug) }}">Resume</a>
                </div>
            </div>
        @empty
            <p class="muted">No courses in progress.</p>
        @endforelse
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Trending</h3>
        @forelse($trending as $course)
            <div class="course-item">

                <div class="details">
                    <div class="title-row">
                        <strong class="title">{{ $course->title }}</strong>
                        <span class="badge">{{ ucfirst($course->category) }}</span>
                        <span class="badge">{{ difficulty_badge($course) }}</span>
                    </div>
                    <div class="muted">👀 {{ number_format($course->views_count) }} · 👥 {{ number_format($course->enrollments_count) }}</div>
                    <a class="badge" href="{{ route('courses.show', $course->slug) }}">View</a>
                </div>
            </div>
        @empty
            <p class="muted">No trending courses at the moment.</p>
        @endforelse
    </div>
</div>
@endsection

@php
function difficulty_badge($course) {
    $tags = $course->tags_array;
    $hasAdvanced = collect($tags)->contains(function($t){ return Str::contains(Str::lower($t), 'advanced'); });
    return $hasAdvanced ? 'Advanced' : 'Beginner-Friendly';
}

@endphp

@push('styles')
<style>
.grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
.card { border:1px solid var(--border); border-radius:10px; padding:12px; }
.course-item { display:flex; gap:12px; align-items:flex-start; margin-bottom:10px; }
.thumb { width:100px; height:66px; background:#eef2f7; border:1px solid var(--border); border-radius:8px; flex-shrink:0; }
.details { flex:1; }
.title-row { display:flex; gap:8px; align-items:center; }
.badge { border:1px solid var(--border); border-radius:999px; padding:4px 8px; font-size:12px; }
.muted { color: var(--muted); }
@media (max-width: 820px) {
  .grid { grid-template-columns: 1fr; }
}
</style>
@endpush