@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row" style="justify-content: space-between; align-items:center; margin-bottom: 16px;">
        <div class="title" style="font-size:22px;">Admin: Recommendations Dashboard</div>
        <form method="POST" action="{{ route('admin.recommendations.refresh') }}">
            @csrf
            <button class="badge" style="background: var(--accent-2); border-color: var(--accent-2); color:#fff;">Refresh Recommendations</button>
        </form>
    </div>

    @if(session('status'))
    <div class="badge" style="background: var(--accent); border-color: var(--accent); color:#fff;">{{ session('status') }}</div>
    @endif
    @if(session('error'))
    <div class="badge" style="background: #ef4444; border-color: #ef4444; color:#fff;">{{ session('error') }}</div>
    @endif

    <!-- Analytics Overview -->
    <div class="card" style="margin-bottom:16px;">
        <div class="row" style="justify-content: space-between; align-items:center;">
            <div class="title" style="font-size:18px;">Analytics Overview</div>
            <div class="muted">Window: last {{ $days }} days</div>
        </div>
        <div class="grid" style="grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top:12px;">
            <div>
                <div class="muted">Total Recommendations (System + Manual)</div>
                <div class="title" style="font-size: 20px;">{{ number_format($totalRecommendationsCurrent) }}</div>
                <div class="muted">System: {{ count($trending) }} · Manual active: {{ number_format($featuredActiveNow) }}</div>
            </div>
            <div>
                <div class="muted">Personalized Recipients (approx)</div>
                <div class="title" style="font-size: 20px;">{{ number_format($personalizedUsersLastNDays) }}</div>
                <div class="muted">Users with recent activity</div>
            </div>
            <div>
                <div class="muted">Engagement Rate (views→enroll)</div>
                <div class="title" style="font-size: 20px;">{{ $engagementRate }}%</div>
                <div class="muted">Avg Rating: {{ number_format($avgRatingOnRecommended, 2) }}</div>
            </div>
        </div>
        <div class="row" style="gap:12px; margin-top:12px; align-items:flex-start;">
            <div style="flex:1;">
                <div class="muted" style="margin-bottom:6px;">Most Recommended Courses</div>
                @forelse($mostRecommendedCourses as $course)
                    <div class="row" style="justify-content: space-between;">
                        <div>{{ $course->title }}</div>
                        <a class="badge" href="{{ route('courses.show', $course->slug) }}">Open</a>
                    </div>
                @empty
                    <div class="muted">No recommended courses.</div>
                @endforelse
            </div>
            <div style="flex:1;">
                <div class="muted" style="margin-bottom:6px;">Trending Tags</div>
                @forelse($trendingTags as $tag => $cnt)
                    <div class="row" style="justify-content: space-between;">
                        <div>#{{ $tag }}</div>
                        <div class="muted">{{ $cnt }}</div>
                    </div>
                @empty
                    <div class="muted">No trending tags.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: repeat(3, 1fr); gap: 12px;">
        <div class="card">
            <div class="muted">Total Course Views</div>
            <div class="title" style="font-size: 20px;">{{ number_format($totalViews) }}</div>
        </div>
        <div class="card">
            <div class="muted">Total Enrollments</div>
            <div class="title" style="font-size: 20px;">{{ number_format($totalEnrollments) }}</div>
        </div>
        <div class="card">
            <div class="muted">Completions (Last {{ $days }} Days)</div>
            <div class="title" style="font-size: 20px;">{{ number_format($completionsLast7Days) }}</div>
        </div>
    </div>

    <div class="row" style="margin-top: 18px; align-items:center; justify-content: space-between;">
        <div class="title" style="font-size:18px;">Trending Courses</div>
        <div class="muted">Top {{ count($trending) }} · Weighted by activity, views, enrollments</div>
    </div>
    <div class="grid" style="grid-template-columns: repeat(2, 1fr); gap: 12px;">
        @foreach($trending as $course)
        <div class="card">
            <div class="row" style="justify-content: space-between;">
                <div>
                    <div class="title">{{ $course->title }}</div>
                    <div class="muted">{{ $course->category ?? '—' }} · ⭐ {{ number_format($course->rating, 1) }} · 👀 {{ $course->views_count }} · 👥 {{ $course->enrollments_count }}</div>
                </div>
                <div class="row" style="gap:6px;">
                    <a class="badge" href="{{ route('courses.show', $course->slug) }}">Open</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row" style="margin-top: 24px; align-items:center; justify-content: space-between;">
        <div class="title" style="font-size:18px;">Recommendation Logs</div>
        <div class="muted">System-generated signals & manual picks</div>
    </div>
    <div class="grid" style="grid-template-columns: 1fr; gap: 12px;">
        <div class="card">
            <div class="muted" style="margin-bottom:8px;">Manual Featured Courses</div>
            @if(!Schema::hasTable('featured_recommendations'))
                <div class="muted">Table missing. Run migrations to enable manual picks.</div>
            @else
                @forelse($featured as $f)
                <div class="row" style="justify-content: space-between;">
                    <div>
                        <div class="title">{{ $f->course->title ?? 'Unknown Course' }}</div>
                        <div class="muted">Added by {{ optional($f->user)->name ?? 'Admin' }} · {{ $f->created_at->diffForHumans() }} @if($f->note) · {{ $f->note }} @endif</div>
                    </div>
                    <form method="POST" action="{{ route('admin.recommendations.featured.destroy', $f->id) }}">
                        @csrf
                        <button class="badge" style="background: #ef4444; border-color: #ef4444; color:#fff;">Remove</button>
                    </form>
                </div>
                @empty
                <div class="muted">No manual featured courses yet.</div>
                @endforelse
            @endif
        </div>

        <div class="card">
            <div class="muted" style="margin-bottom:8px;">Recent Course Activity (Signals)</div>
            @forelse($recentActivities as $a)
                <div class="row" style="justify-content: space-between;">
                    <div class="muted">{{ ucfirst($a->type) }} · Course #{{ $a->course_id }} @if($a->lesson_id) · Lesson #{{ $a->lesson_id }} @endif</div>
                    <div class="muted">{{ $a->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="muted">No recent activity.</div>
            @endforelse
        </div>
    </div>

    <div class="row" style="margin-top: 24px; align-items:center; justify-content: space-between;">
        <div class="title" style="font-size:18px;">Manage Recommendations</div>
        <div class="muted">Add/remove featured courses</div>
    </div>
    <div class="card">
        @if(!Schema::hasTable('featured_recommendations'))
            <div class="muted">Table missing. Run migrations to enable manual picks.</div>
        @else
            <div class="muted" style="margin-top:10px;">Quick add from latest courses</div>
            <div class="grid" style="grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top:6px;">
                @foreach($latestCourses as $c)
                <div class="card">
                    <div class="row" style="justify-content: space-between;">
                        <div>
                            <div class="title">{{ $c->title }}</div>
                            <div class="muted">{{ $c->category ?? '—' }} · ⭐ {{ number_format($c->rating, 1) }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.recommendations.featured.store') }}">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $c->id }}" />
                            <button class="badge">Feature</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection