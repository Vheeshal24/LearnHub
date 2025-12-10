@extends('layouts.app')

@section('title', 'Admin: Recommendation Settings')

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <h1 style="margin:0;">Admin / Smart Recommendation Settings</h1>
    <a href="{{ route('admin.courses.index') }}" class="pill">← Back to Courses</a>
</div>

@if(session('status'))
    <div class="card" style="margin-bottom:12px;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('admin.recommendations.settings.update') }}" class="card" style="margin-top:12px;">
    @csrf
    <div class="row" style="gap:12px;">
        <div style="flex:1;">
            <label>Views Weight</label>
            <input type="number" name="views_weight" step="0.1" min="0" value="{{ old('views_weight', $settings->views_weight) }}" required />
            <div class="muted">Multiplier applied to course views</div>
        </div>
        <div style="flex:1;">
            <label>Enrollments Weight</label>
            <input type="number" name="enrollments_weight" step="0.1" min="0" value="{{ old('enrollments_weight', $settings->enrollments_weight) }}" required />
            <div class="muted">Multiplier applied to enrollments</div>
        </div>
        <div style="flex:1;">
            <label>Recent Activity Weight</label>
            <input type="number" name="activity_weight" step="0.1" min="0" value="{{ old('activity_weight', $settings->activity_weight) }}" required />
            <div class="muted">Multiplier applied to recent activity counts</div>
        </div>
    </div>

    <div class="row" style="gap:12px; margin-top:12px;">
        <div style="flex:1;">
            <label>Default Trending Window (days)</label>
            <input type="number" name="default_trending_days" min="1" max="365" value="{{ old('default_trending_days', $settings->default_trending_days) }}" required />
            <div class="muted">Used as default when days not specified</div>
        </div>
        <div style="flex:1;">
            <label>Personalized Top Tags Limit</label>
            <input type="number" name="personalized_top_tags_limit" min="1" max="50" value="{{ old('personalized_top_tags_limit', $settings->personalized_top_tags_limit) }}" required />
            <div class="muted">How many top tags to consider</div>
        </div>
    </div>

    <div class="row" style="justify-content:flex-end; gap:8px; margin-top:16px;">
        <button type="submit">Save Settings</button>
    </div>
</form>
@endsection