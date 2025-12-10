@extends('layouts.app')

@section('title', 'Recommendation Logs')

@section('content')
<div class="container">
    <div class="row" style="justify-content: space-between; align-items: baseline;">
        <h1 class="title" style="font-size: 22px;">Recommendation Logs</h1>
        <span class="muted" style="font-size:13px;">Read-only overview. Manual entries are deletable.</span>
    </div>

    @php
        $systemCount = is_countable($system) ? count($system) : 0;
        $maxScore = max(1, (is_object($system) && method_exists($system, 'pluck')) ? $system->pluck('score')->max() : 1);
    @endphp

    <div class="card" style="margin: 16px 0;">
        <div class="row" style="justify-content: space-between;">
            <div class="row" style="gap:8px;">
                <span class="badge">Manual: {{ $manual->total() }}</span>
                <span class="badge">System: {{ $systemCount }}</span>
            </div>
            <form method="GET" action="{{ route('admin.recommendations.logs.index') }}" class="row" style="gap:8px;">
                <label class="muted" style="font-size:12px;">Trending window</label>
                <input type="number" min="1" max="365" name="days" value="{{ $days }}" placeholder="Days" style="width:100px;" />
                <label class="muted" style="font-size:12px;">System limit</label>
                <input type="number" min="1" max="100" name="limit" value="{{ $limit }}" placeholder="Limit" style="width:90px;" />
                <button type="submit">Apply</button>
            </form>
        </div>
    </div>

    <div class="card" style="padding:0;">
        <div class="row" style="justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--border);">
            <div class="title">All Recommendation Entries</div>
            <div class="muted" style="font-size:12px;">Columns: ID · Course · Recommended · Type · Score · Created · Actions</div>
        </div>
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background:#f3f4f6; text-align:left;">
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border); width:70px;">ID</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border);">Course</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border);">Recommended Course</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border); width:110px;">Type</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border); width:140px;">Score</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border); width:160px;">Created At</th>
                        <th style="padding:10px 12px; border-bottom:1px solid var(--border); width:160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($manual->count() === 0 && $systemCount === 0)
                        <tr>
                            <td colspan="7" style="padding:16px; text-align:center;" class="muted">No recommendation entries yet.</td>
                        </tr>
                    @endif

                    {{-- Manual entries from FeaturedRecommendations --}}
                    @foreach($manual as $f)
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:10px 12px;">{{ $f->id }}</td>
                            <td style="padding:10px 12px;" class="muted">&mdash;</td>
                            <td style="padding:10px 12px;">
                                @if($f->course)
                                    <a href="{{ route('courses.show', $f->course->slug) }}" target="_blank">{{ $f->course->title }}</a>
                                @else
                                    <span class="muted">Unknown</span>
                                @endif
                            </td>
                            <td style="padding:10px 12px;">
                                <span class="badge" style="background:#fff8e1; border-color:#facc15; color:#a16207;">Manual</span>
                            </td>
                            <td style="padding:10px 12px;">
                                @if(!is_null($f->priority))
                                    <div class="row" style="gap:8px; align-items:center;">
                                        <span class="badge">Priority {{ $f->priority }}</span>
                                    </div>
                                @else
                                    <span class="muted">&mdash;</span>
                                @endif
                            </td>
                            <td style="padding:10px 12px;">{{ optional($f->created_at)->format('Y-m-d H:i') }}</td>
                            <td style="padding:10px 12px;">
                                <div class="row" style="gap:6px;">
                                    <a href="{{ route('admin.recommendations.featured.edit', $f->id) }}" class="badge">Edit</a>
                                    <form method="POST" action="{{ route('admin.recommendations.featured.destroy', $f->id) }}" onsubmit="return confirm('Delete this manual recommendation?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:#ef4444;border-color:#ef4444;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    {{-- System suggestions (Trending) --}}
                    @foreach($system as $s)
                        @php $percent = min(100, round(($s['score'] / $maxScore) * 100)); @endphp
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:10px 12px;" class="muted">&mdash;</td>
                            <td style="padding:10px 12px;" class="muted">&mdash;</td>
                            <td style="padding:10px 12px;">
                                <a href="{{ route('courses.show', $s['recommended']->slug) }}" target="_blank">{{ $s['recommended']->title }}</a>
                            </td>
                            <td style="padding:10px 12px;">
                                <span class="badge" style="background:#ecfeff; border-color:#06b6d4; color:#0e7490;">System</span>
                            </td>
                            <td style="padding:10px 12px;">
                                <div class="row" style="gap:8px; align-items:center;">
                                    <span class="muted">{{ number_format($s['score'], 2) }}</span>
                                    <div style="flex:1; height:8px; background:#e5e7eb; border-radius:999px; overflow:hidden;">
                                        <div style="width: {{ $percent }}%; height:100%; background:#06b6d4;"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:10px 12px;">{{ optional($s['created_at'])->format('Y-m-d H:i') ?? '—' }}</td>
                            <td style="padding:10px 12px;" class="muted">—</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 12px 16px; border-top:1px solid var(--border);">
            {{ $manual->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection