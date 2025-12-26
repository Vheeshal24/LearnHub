@extends('layouts.app')

@section('title', $course->title . ' — LearnTech')

@php
if (!function_exists('difficulty_badge')) {
    function difficulty_badge($course) {
        $tags = $course->tags_array;
        $hasAdvanced = collect($tags)->contains(function($t){ return Str::contains(Str::lower($t), 'advanced'); });
        return $hasAdvanced ? 'Advanced' : 'Beginner-Friendly';
    }
}
@endphp

@section('content')
    <div class="hero">
        <div style="flex: 1; min-width: 260px;">
            <div class="row" style="justify-content:flex-start; gap:10px;">
                <span class="badge" style="color:#a7f3d0;border-color:#10b98144;background:transparent;">{{ ucfirst($course->category) }}</span>
                <span class="badge" style="color:#fcd34d;border-color:#f59e0b44;background:transparent;">{{ difficulty_badge($course) }}</span>
            </div>
            <h1 class="title">{{ $course->title }}</h1>

            <div class="row meta">
                <div class="stat">Published {{ $course->published_at ? $course->published_at->diffForHumans() : '—' }}</div>
                <div class="stat">Updated {{ $course->updated_at->diffForHumans() }}</div>
                <div class="stat">Duration {{ $course->lessons->sum('duration_minutes') }} mins</div>
            </div>
        </div>
        <div style="flex: 0 0 320px;">
            <div class="row">
                <div class="stat" style="text-align:center;">
                    <div class="muted">Views</div>
                    <div style="font-weight:600; font-size:22px;">{{ number_format($course->views_count) }}</div>
                </div>
                <div class="stat" style="text-align:center;">
                    <div class="muted">Enrollments</div>
                    <div style="font-weight:600; font-size:22px;">{{ number_format($course->enrollments_count) }}</div>
                </div>
                <div class="stat" style="text-align:center;">
                    <div class="muted">Rating</div>
                    <div style="font-weight:600; font-size:22px;">{{ number_format($course->rating, 1) }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('courses.enroll', $course->slug) }}" style="margin-top:12px;">
                @csrf
                <button type="submit" style="width:100%; background: var(--accent); border-color: var(--accent); color:#fff;">Enroll</button>
            </form>

            @if (session('status'))
                <div class="alert success" style="margin-top:10px;">{{ session('status') }}</div>
            @endif

            <div class="progress" style="margin-top:12px;">
                <div class="progress-bar" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="muted" style="text-align:center;">Your progress: {{ $progressPercent }}%</div>
        </div>
    </div>

    <div class="panel">
        <h2>Course Description</h2>
        <p>{{ $course->description }}</p>
        @php($tags = $course->tags_array)
        @if(!empty($tags))
            <h3 style="margin-top:10px;">You will learn</h3>
            <div class="row">
                @foreach ($tags as $t)
                    <span class="badge" style="color:black;border-color:#10b98144;">#{{ $t }}</span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="panel">
        <h2>Lessons</h2>
        @forelse ($course->lessons as $lesson)
            <details style="margin:10px 0;" class="lesson-item" data-lesson-id="{{ $lesson->id }}">
                <summary class="lesson-summary">
                    <div class="row" style="gap:10px;">
                        <span class="badge">#{{ $lesson->position }}</span>
                        <strong style="flex:1;">{{ $lesson->title }}</strong>
                        <span class="muted">{{ $lesson->duration_minutes }} mins</span>
                        @if($lesson->type === 'video')
                            <span class="badge" style="color:#93c5fd;border-color:#3b82f644;">Video</span>
                        @elseif($lesson->type === 'quiz')
                            <span class="badge" style="color:#fcd34d;border-color:#f59e0b44;">Quiz</span>
                        @else
                            <span class="badge">Article</span>
                        @endif
                    </div>
                </summary>
                <div style="padding:8px 10px;">
                    <p>{{ $lesson->summary }}</p>
                    <div class="row" style="margin-top:8px;">
                        <a class="badge" href="{{ route('lessons.show', [$course->slug, $lesson->slug]) }}">Open lesson</a>
                        @auth
                        <form method="POST" action="{{ route('favorites.toggle', ['course' => $course->id]) }}">
                            @csrf
                            <button type="submit" class="badge" style="color:#fde68a;border-color:#f59e0b44;background:transparent;">Favorite</button>
                        </form>
                        @else
                        <a class="badge" href="{{ route('login') }}">Login to favorite</a>
                        @endauth
                    </div>
                </div>
            </details>
        @empty
            <p class="muted">No lessons available yet.</p>
        @endforelse
    </div>

    <div class="panel">
        <h2>Related Courses</h2>
        <div class="grid scroll-x" style="gap:12px;">
            @forelse ($relatedCourses as $rc)
                <div class="card" style="min-width:280px;">

                    <h3 style="margin:0 0 6px; font-size:16px;">{{ $rc->title }}</h3>
                    <div class="muted">{{ ucfirst($rc->category) }} · {{ number_format($rc->rating, 1) }} ★</div>
                    <a class="badge" style="margin-top:8px;" href="{{ route('courses.show', $rc->slug) }}">View</a>
                </div>
            @empty
                <p class="muted">No related courses found.</p>
            @endforelse
        </div>
    </div>

    <!-- Removed duplicate assistant button: centralized in layout -->

@endsection



@push('styles')
    <style>
        .hero { display:flex; gap:20px; align-items:flex-start; }
        .title { margin:4px 0 4px; font-size:28px; }
        .row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .badge { border:1px solid var(--border); border-radius:999px; padding:6px 10px; font-size:12px; }
        .muted { color: var(--muted); }
        .panel { border:1px solid var(--border); border-radius:10px; padding:14px; margin:12px 0; }
        .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:10px; }
        .card { border:1px solid var(--border); border-radius:10px; padding:10px; }
        .scroll-x { overflow-x: auto; display: grid; grid-auto-flow: column; }
        .progress { width:100%; background: var(--border); border-radius:999px; height:8px; }
        .progress-bar { height:8px; background: var(--accent); border-radius:999px; }
        .lesson-item { border:1px dashed #cbd5e1; border-radius:10px; }
        .lesson-summary { padding:8px 10px; }
        .stat { border:1px solid var(--border); border-radius:10px; padding:10px; }
        .assistant-button { position: fixed; bottom: 20px; right: 20px; background: var(--accent-2); border-color: var(--accent-2); color:#fff; }
    </style>
@endpush

@push('scripts')
<script>
    // Persist lesson expansion state per course
    (function(){
        const courseKey = 'course_lessons_open_' + {{ $course->id }};
        const items = document.querySelectorAll('.lesson-item');

        function saveState() {
            const openIds = Array.from(items).filter(d => d.open).map(d => d.getAttribute('data-lesson-id'));
            localStorage.setItem(courseKey, JSON.stringify(openIds));
        }

        function restoreState() {
            try {
                const raw = localStorage.getItem(courseKey);
                if (!raw) return;
                const openIds = JSON.parse(raw);
                items.forEach(d => {
                    const id = d.getAttribute('data-lesson-id');
                    if (openIds.includes(id)) d.open = true;
                });
            } catch(e) {}
        }

        items.forEach(d => d.addEventListener('toggle', saveState));
        restoreState();
    })();
</script>
@endpush