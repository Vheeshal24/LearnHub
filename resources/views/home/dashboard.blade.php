@extends('layouts.app')

@section('title', 'LearnTech — Dashboard')

@section('content')
<div class="grid">
    <div class="card">
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 8px;">
        <h2 style="margin: 0; line-height: 1;">Welcome back!</h2>
        
        <a href="{{ route('analytics.dashboard') }}" class="analytics-btn" style="padding: 6px 16px; font-size: 14px;">
            <i class="fas fa-chart-line"></i> Learning Analytics
        </a>
    </div>
    <p class="muted" style="margin: 0;">Here’s your learning overview and recommended courses.</p>
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
                    <div class="muted">Progress: {{ $progress[$course->id] ?? 0 }}%</div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
.card { border:1px solid var(--border); border-radius:10px; padding:12px; }
.course-item { display:flex; gap:12px; align-items:flex-start; margin-bottom:10px; }
.thumb { width:100px; height:66px; background:#eef2f7; border:1px solid var(--border); border-radius:8px; flex-shrink:0; }
.details { flex:1; }
.title-row { display:flex; gap:8px; align-items:center; }
.badge { border:1px solid var(--border); border-radius:999px; padding:4px 8px; font-size:12px; }
.muted { color: var(--muted); }

.analytics-btn {
    background-color: #10afd7ff;
    color: white !important;
    border: none;
    padding: 8px 25px;
    border-radius: 17px;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    transition: background-color 0.2s;
}
.analytics-btn:hover {
    background-color: #10afd7ff;
}

@media (max-width: 820px) {
  .grid { grid-template-columns: 1fr; }
  .card[style*="display: flex"] {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 10px;
  }
}
</style>
@endpush