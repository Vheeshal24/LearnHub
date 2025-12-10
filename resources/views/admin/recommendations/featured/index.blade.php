@extends('layouts.app')

@section('title', 'Admin: Featured Recommendations')

@section('content')
<div class="row" style="justify-content: space-between; margin-bottom: 16px;">
    <h1 style="margin:0;">Admin / Featured Recommendations</h1>
    <div class="row" style="gap:8px;">
        <a href="{{ route('admin.recommendations.featured.create') }}" class="pill" style="background: var(--accent); border-color: var(--accent); color:#fff;">+ New Featured</a>
        <a href="{{ route('admin.recommendations.dashboard') }}" class="pill">Dashboard</a>
        <a href="{{ route('admin.recommendations.settings') }}" class="pill">Settings</a>
    </div>
</div>

<form method="GET" action="{{ route('admin.recommendations.featured.index') }}" class="card" style="margin-bottom:16px;">
    <div class="row" style="gap:8px;">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search by course title, tags, or category" style="flex:1;"/>
        <select name="active" style="width:180px;">
            <option value="">All statuses</option>
            <option value="1" @selected($active==='1')>Active</option>
            <option value="0" @selected($active==='0')>Inactive</option>
        </select>
        <button type="submit">Filter</button>
    </div>
</form>

@if ($items->count() === 0)
    <div class="card">No featured recommendations found.</div>
@else
    <div class="grid">
        @foreach ($items as $item)
            <div class="card">
                <div class="row" style="justify-content: space-between;">
                    <div>
                        <div style="font-weight:600; font-size:18px;">
                            {{ $item->course->title }}
                        </div>
                        <div class="muted">Priority: {{ $item->priority }} · Status: {{ $item->active ? 'Active' : 'Inactive' }}</div>
                        @if ($item->starts_at || $item->ends_at)
                            <div class="muted">Window: {{ optional($item->starts_at)->format('Y-m-d') ?? '—' }} → {{ optional($item->ends_at)->format('Y-m-d') ?? '—' }}</div>
                        @endif
                        @if ($item->note)
                            <div style="margin-top:6px;">Note: {{ $item->note }}</div>
                        @endif
                    </div>
                    <div class="row" style="gap:8px;">
                        <a href="{{ route('admin.recommendations.featured.edit', $item->id) }}" class="pill">Edit</a>
                        <form method="POST" action="{{ route('admin.recommendations.featured.destroy', $item->id) }}" onsubmit="return confirm('Delete this featured recommendation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pill" style="background:#ef4444;border-color:#ef4444;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div style="margin-top:16px;">
        {{ $items->links() }}
    </div>
@endif
@endsection