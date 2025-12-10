@extends('layouts.app')

@section('title', 'LearnTech — Learn Faster, Get Skilled')

@section('content')
    <section class="card" style="padding:28px; background: linear-gradient(120deg,#0a7cff22,#10b98122); border-color:var(--border);">
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
            <div style="flex:1; min-width:280px;">
                <h1 style="margin:0 0 10px; font-size:28px;">Level up your skills with interactive lessons</h1>
                <p class="muted" style="margin:0 0 14px; max-width:620px;">Master tech topics with concise lessons and built-in quizzes. Track your progress, and learn at your own pace.</p>
                <div class="row" style="gap:10px;">
                    <a class="badge" style="background: var(--accent); border-color: var(--accent); color:#fff;" href="{{ route('browse') }}">Browse Courses</a>
                    @guest
                        <a class="badge" style="background: var(--accent-2); border-color: var(--accent-2); color:#fff;" href="{{ route('register') }}">Get Started</a>
                    @endguest
                    @auth
                        <span class="pill">Welcome back, {{ auth()->user()->name }}</span>
                    @endauth
                </div>
            </div>
            @if($featured)
                <div style="flex:1; min-width:280px;">
                    <div class="card" style="background:#fff;">
                        <div class="pill" style="background:#f3f4f6; margin-bottom:8px;">Featured Course</div>
                        <h2 style="margin:0 0 6px; font-size:18px;">{{ $featured->title }}</h2>
                        @if ($featured->description)
                            <p class="muted" style="margin:8px 0;">{{ Str::limit($featured->description, 140) }}</p>
                        @endif
                        <div class="row" style="gap:6px;">
                            <span class="badge">Category: {{ ucfirst($featured->category) }}</span>
                            <span class="badge">Rating: {{ number_format($featured->rating, 1) }}</span>
                            <span class="badge">Enrollments: {{ $featured->enrollments_count }}</span>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <a class="badge" href="{{ route('courses.show', $featured->slug) }}">Start Learning</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section style="margin-top:22px;">
        <h3 style="margin:0 0 10px;">Trending Now</h3>
        <div class="grid">
            @forelse ($trending as $course)
                <div class="card">
                    <h4 style="margin:0 0 6px; font-size:16px;">{{ $course->title }}</h4>
                    <div class="muted">{{ ucfirst($course->category) }} · ⭐ {{ number_format($course->rating, 1) }} · 👁️ {{ $course->views_count }}</div>
                    @php($tags = $course->tags_array)
                    @if(!empty($tags))
                        <div class="row" style="margin-top:6px;">
                            @foreach ($tags as $t)
                                <span class="badge" style="color:#a7f3d0;border-color:#10b98144;">#{{ $t }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="row" style="margin-top:10px;">
                        <a class="badge" href="{{ route('courses.show', $course->slug) }}">View Course</a>
                    </div>
                </div>
            @empty
                <p class="muted">No trending courses yet.</p>
            @endforelse
        </div>
    </section>

    <section style="margin-top:22px;">
        <h3 style="margin:0 0 10px;">New Arrivals</h3>
        <div class="grid">
            @forelse ($newCourses as $course)
                <div class="card">
                    <h4 style="margin:0 0 6px; font-size:16px;">{{ $course->title }}</h4>
                    <div class="muted">{{ ucfirst($course->category) }} · Published {{ optional($course->published_at ?? $course->created_at)->diffForHumans() }}</div>
                    <div class="row" style="margin-top:10px;">
                        <a class="badge" href="{{ route('courses.show', $course->slug) }}">View Course</a>
                    </div>
                </div>
            @empty
                <p class="muted">No new courses yet.</p>
            @endforelse
        </div>
    </section>

    <section style="margin-top:22px;">
        <h3 style="margin:0 0 10px;">Browse by Category</h3>
        <div class="row" style="flex-wrap:wrap; gap:8px;">
            @forelse ($categories as $cat)
                <a class="badge" href="{{ route('browse', ['category' => $cat]) }}">{{ ucfirst($cat) }}</a>
            @empty
                <p class="muted">Categories will appear as courses are added.</p>
            @endforelse
        </div>
    </section>
    @auth
        @if(isset($recommendedCourses) && $recommendedCourses->count())
            <section style="margin-top:22px;">
                <h3 style="margin:0 0 10px;">Recommended For You</h3>
                <div class="grid">
                    @foreach ($recommendedCourses as $course)
                        <div class="card">
                            <h4 style="margin:0 0 6px; font-size:16px;">{{ $course->title }}</h4>
                            <div class="muted">{{ ucfirst($course->category) }} · ⭐ {{ number_format($course->rating, 1) }} · 👥 {{ $course->enrollments_count }}</div>
                            @php($tags = $course->tags_array)
                            @if(!empty($tags))
                                <div class="row" style="margin-top:6px;">
                                    @foreach ($tags as $t)
                                        <span class="badge" style="color:#a7f3d0;border-color:#10b98144;">#{{ $t }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row" style="margin-top:10px;">
                                <a class="badge" href="{{ route('courses.show', $course->slug) }}">View Course</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if(isset($recommendedLessons) && $recommendedLessons->count())
            <section style="margin-top:22px;">
                <h3 style="margin:0 0 10px;">Recommended Lessons</h3>
                <div class="grid">
                    @foreach ($recommendedLessons as $lesson)
                        <div class="row" style="justify-content: space-between; border: 1px solid var(--border); background: #fff; border-radius: 8px; padding: 10px 12px;">
                            <div>
                                <div style="font-weight:600;color:var(--text);">{{ $lesson->title }}</div>
                                <div class="muted">{{ optional($lesson->course)->title }} · Lesson {{ $lesson->position }}</div>
                            </div>
                            <a class="badge" href="{{ route('lessons.show', [$lesson->course->slug, $lesson->slug]) }}">Open</a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endauth
@endsection