@extends('layouts.app')
@section('title', 'LearnHub — Analytics')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{--SUCCESS--}} 
<div id="success-modal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-icon-wrapper success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="modal-title">Success!</h3>
        <p class="modal-text" id="success-message-text"></p>
        <div class="modal-actions">
            <button onclick="closeSuccessModal()" class="btn-modal-confirm success-btn">Okay</button>
        </div>
    </div>
</div>

{{-- CONFIRMATION --}}
<div id="confirmation-modal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-icon-wrapper danger-icon">
            <i class="fas fa-trash-alt"></i>
        </div>
        <h3 class="modal-title">Remove Learning Goal?</h3>
        <p class="modal-text">Are you sure you want to delete this goal? This action cannot be undone.</p>
        <div class="modal-actions">
            <button onclick="closeConfirmModal()" class="btn-modal-cancel">Cancel</button>
            <button onclick="proceedWithRemoval()" class="btn-modal-confirm danger-btn">Yes, Remove</button>
        </div>
    </div>
</div>

<div class="analytics-wrapper">
    {{-- HEADER --}}
    <div class="page-header">
        <div class="header-content">
            <h2 class="page-title">Learning Analytics</h2>
            <p class="page-subtitle">Track your course progress and learning deadlines.</p>
        </div>
        <div class="header-controls">
            <form method="GET" action="{{ route('analytics.dashboard') }}" class="filter-form">
                <div class="select-wrapper">
                    <select name="course_id" onchange="this.form.submit()" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div class="button-group">
                <a href="{{ route('analytics.exportPDF', ['course_id' => request('course_id')]) }}" class="btn-pill"><i class="fas fa-file-pdf"></i> PDF</a>
                <a href="{{ route('analytics.exportCSV', ['course_id' => request('course_id') ?? 'all']) }}" class="btn-pill"> <i class="fas fa-file-csv"></i> CSV</a>
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="kpi-row">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Active Courses</span>
                <div class="icon-circle"><i class="fas fa-book"></i></div>
            </div>
            <div class="stat-value">{{ $totalCoursesEnrolled ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Avg. Completion</span>
                <div class="icon-circle"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="stat-value">{{ round($averageProgress ?? 0, 1) }}<span class="stat-unit">%</span></div>
            <div class="stat-progress">
                <div class="stat-progress-bar" style="width: {{ $averageProgress ?? 0 }}%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Quiz Attempts</span>
                <div class="icon-circle"><i class="fas fa-clipboard-check"></i></div>
            </div>
            <div class="stat-value">{{ $totalQuizzesTaken ?? 0 }}</div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="chart-row">
        <div class="content-card">
            <h3 class="section-heading">Course Progress</h3>
            <div class="chart-wrapper">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
        <div class="content-card">
            <h3 class="section-heading">Course Proficiency</h3>
            <div class="chart-wrapper flex-center">
                <canvas id="quizChart"></canvas>
            </div>
        </div>
    </div>

    {{-- RECENT HISTORY --}}
    <div class="content-card">
        <h3 class="section-heading">Recent Activity</h3>
        <div class="table-responsive history-scroll">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th style="width: 20%">Date</th>
                        <th>Course & Lesson</th>
                        <th style="text-align:center;">Score</th>
                        <th style="text-align:right;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizHistory as $attempt)
                        <tr>
                            <td class="text-muted text-sm">
                                {{ \Carbon\Carbon::parse($attempt->created_at)->format('M d, Y') }}
                                <div class="text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($attempt->created_at)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-medium text-dark">{{ $attempt->lesson_title }}</div>
                               <div style="font-weight: bold; color: #4f46e5; font-size: 14px;">{{ $attempt->course_title }}</div>
                            </td>
                            <td style="text-align:center;">
                                <span class="score-pill {{ $attempt->percentage_score >= 100 ? 'score-perfect' : 'score-average' }}">
                                    {{ $attempt->percentage_score }}%
                                </span>
                            </td>
                            <td style="text-align:right;">
                                @if($attempt->percentage_score >= 100)
                                    <span class="status-text success">Passed</span>
                                @else
                                    <span class="status-text warning">Retake</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No recent activity.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- OVERALL COURSE SUMMARY --}}
    <div class="content-card">
        <h3 class="section-heading">Overall Course Summary</h3>
        <div class="table-responsive">
            <table class="clean-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 25%">Course Name</th>
                        <th style="width: 20%">Completion</th>
                        <th style="width: 20%">Progress</th>
                        <th style="text-align:center;">Status</th>
                        <th style="width: 30%; text-align:right;">Action Plan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analytics as $a)
                        @php
                            $goal = $goals->get($a['course_id']);
                            $hasGoal = $goal && $goal->target_completion_time;
                            $endTimeJs = 0;
                            $isOverdue = false;
                            if ($hasGoal) {
                                $target = \Carbon\Carbon::parse($goal->target_completion_time);
                                $endTimeJs = $target->timestamp * 1000;
                                if ($target->isPast()) $isOverdue = true;
                            }
                        @endphp
                        <tr>
                            <td class="font-medium text-dark">{{ $a['course_title'] }}</td>
                            <td class="text-center text-muted text-sm">{{ $a['completed_lessons'] }} / {{ $a['total_lessons'] }}</td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: {{ $a['progress'] }}%;"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($a['status'] == 'Completed')
                                    <span class="status-badge success">Completed</span>
                                @else
                                    <span class="status-badge neutral">Retake</span>
                                @endif
                            </td>
                            <td style="text-align:right; padding-right: 0;">
                                @if($a['status'] == 'Completed')
                                    <span class="text-muted text-xs" style="padding-right: 15px;">—</span>
                                @else
                                    @if($hasGoal)
                                        <div class="goal-card-simple">
                                            <div class="goal-top">
                                                @if($isOverdue)
                                                    <span class="goal-time text-danger">
                                                        <i class="fas fa-exclamation-circle"></i> Overdue
                                                    </span>
                                                @else
                                                    <span class="goal-time text-primary">
                                                        <i class="far fa-clock"></i>
                                                        <span class="countdown-timer" data-end="{{ $endTimeJs }}">Loading...</span>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="goal-note">{{ Str::limit($goal->goal_description, 25) }}</div>
                                            <div class="goal-links">
                                                <a href="{{ route('learning_goals.edit', $goal->id) }}" class="link-edit">Edit</a>
                                                <span class="link-sep">|</span>
                                                <form action="{{ route('learning_goals.destroy', $goal->id) }}" method="POST" style="display:inline;" onsubmit="confirmRemove(event, this);">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="link-remove">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ route('learning_goals.create', ['course_id' => $a['course_id']]) }}" class="btn-set-goal">
                                            <i class="fas fa-plus"></i> Set Goal
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    @if(session('success'))
        showSuccessModal(@json(session('success')));
    @endif
});

// Success 
function showSuccessModal(message) {
    const modal = document.getElementById('success-modal');
    document.getElementById('success-message-text').innerText = message;
    modal.style.display = 'flex';
    setTimeout(() => { modal.classList.add('active'); }, 10);
}
function closeSuccessModal() {
    const modal = document.getElementById('success-modal');
    modal.classList.remove('active');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
}

// Confirm Removal
let formToSubmit = null;
function confirmRemove(event, form) {
    event.preventDefault();
    formToSubmit = form;
    const modal = document.getElementById('confirmation-modal');
    modal.style.display = 'flex';
    setTimeout(() => { modal.classList.add('active'); }, 10);
}
function closeConfirmModal() {
    const modal = document.getElementById('confirmation-modal');
    modal.classList.remove('active');
    setTimeout(() => { modal.style.display = 'none'; }, 300);
    formToSubmit = null;
}
function proceedWithRemoval() {
    if(formToSubmit) formToSubmit.submit();
}

// Countdown Timers
function updateTimers() {
    const timers = document.querySelectorAll('.countdown-timer');
    const now = new Date().getTime();
    timers.forEach(timer => {
        const endTime = parseInt(timer.getAttribute('data-end'));
        const distance = endTime - now;
        if(distance < 0) {
            timer.innerText = "Overdue";
            timer.parentElement.classList.remove('text-primary');
            timer.parentElement.classList.add('text-danger');
            return;
        }
        const days = Math.floor(distance / (1000*60*60*24));
        const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
        const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
        const seconds = Math.floor((distance % (1000*60)) / 1000);
        let text = "";
        if(days>0) text += days + "d ";
        text += hours + "h " + minutes + "m " + seconds + "s";
        timer.innerText = text;
    });
}
setInterval(updateTimers, 1000);
updateTimers();

// Charts
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = '#e2e8f0';

const ctxProgress = document.getElementById('progressChart');
new Chart(ctxProgress, {
    type: 'bar',
    data: {
        labels: @json($analytics->pluck('course_title')),
        datasets: [{
            label: 'Completion (%)',
            data: @json($analytics->pluck('progress')),
            backgroundColor: '#4f46e5',
            borderRadius: 4,
            categoryPercentage: 0.8,
            barPercentage: 0.5
        }]
    },
});


const ctxQuiz = document.getElementById('quizChart');
new Chart(ctxQuiz, {
    type: 'doughnut',
    data: {
        labels: ['Passed', 'Failed'],
        datasets: [{
            data: [{{ $passedCount }}, {{ $failedCount }}],
            backgroundColor: ['#10b981','#ef4444'],
            borderWidth: 3,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'right',
                labels: { usePointStyle: true, boxWidth: 4 }
            }
        }
    }
});
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
:root {
    --bg-body: #10cfe3;
    --bg-card: #ffffff;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --primary: #4f46e5;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --radius: 8px;
}

.score-average { background: #fee2e2; color: #991b1b; }

body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-primary); }

/* --- CENTERED MODAL STYLES --- */
.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); z-index: 5000;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.3s ease;
}
.modal-overlay.active { opacity: 1; }

.modal-card {
    background: white; width: 450px; max-width: 90%;
    padding: 40px; border-radius: 16px; text-align: center;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-overlay.active .modal-card { transform: scale(1); }

/* Modal Icons */
.modal-icon-wrapper {
    width: 80px; height: 80px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; margin: 0 auto 20px auto;
}
.danger-icon { background: #fee2e2; color: #ef4444; }
.success-icon { background: #dcfce7; color: #10b981; }

.modal-title { margin: 0 0 10px 0; font-size: 1.5rem; font-weight: 800; color: #1f2937; }
.modal-text { color: #6b7280; margin-bottom: 30px; font-size: 1rem; line-height: 1.5; }
.modal-actions { display: flex; gap: 15px; justify-content: center; }

.btn-modal-cancel {
    background: #f3f4f6; color: #374151; border: none; padding: 12px 24px;
    border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem;
    transition: background 0.2s;
}
.btn-modal-cancel:hover { background: #e5e7eb; }

/* Buttons */
.btn-modal-confirm {
    color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;
    cursor: pointer; font-size: 1rem; transition: background 0.2s;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.danger-btn { background: #ef4444; }
.danger-btn:hover { background: #dc2626; transform: translateY(-1px); }
.success-btn { background: #10b981; width: 100%; }
.success-btn:hover { background: #059669; transform: translateY(-1px); }

/* --- LAYOUT & COMPONENTS --- */
.analytics-wrapper { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; display: flex; flex-direction: column; gap: 1.5rem; }
.page-header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid rgba(255,255,255, 0.2); padding-bottom: 1.5rem; margin-bottom: 0.5rem; }
.header-controls { display: flex; gap: 0.75rem; }
.btn-pill { border: 1px solid var(--border-color); background: white; padding: 0.5rem 1.25rem; border-radius: 50px; color: var(--text-secondary); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
.btn-pill:hover { background: #f1f5f9; color: var(--text-primary); border-color: #cbd5e1; }
.form-select { border: 1px solid var(--border-color); padding: 0.5rem 2rem 0.5rem 1rem; border-radius: 50px; font-size: 0.875rem; color: var(--text-primary); background-color: white; cursor: pointer; outline: none; }
/* Cards */
.kpi-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.stat-card, .content-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.stat-card:hover, .content-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1); }
.stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: var(--text-secondary); }
.icon-circle { width: 36px; height: 36px; background: #eef2ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.stat-value { font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1; }
.stat-unit { font-size: 1rem; color: var(--text-secondary); margin-left: 2px; }
.stat-progress { margin-top: 1rem; height: 4px; background: #f1f5f9; border-radius: 99px; overflow: hidden; }
.stat-progress-bar { height: 100%; background: var(--primary); }

/* Tables */
.section-heading { font-size: 1rem; font-weight: 600; margin: 0 0 1.5rem 0; color: var(--text-primary); }
.chart-row { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
.chart-wrapper { height: 200px; width: 100%;overflow: visible;  }
.flex-center { display: flex; justify-content: center; }
.table-responsive { overflow-x: auto; }
.history-scroll { max-height: 250px; overflow-y: auto; }
.clean-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.875rem; }
.clean-table th { text-align: left; padding: 0.75rem 1rem; font-weight: 600; color: var(--text-secondary); border-bottom: 1px solid var(--border-color); background: #f9fafb; position: sticky; top: 0; white-space: nowrap; }
.clean-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; color: var(--text-primary); vertical-align: middle; }
.clean-table tr:last-child td { border-bottom: none; }

/* Misc */
.score-pill { display: inline-block; padding: 0.15rem 0.6rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem; background: #f1f5f9; color: var(--text-primary); }
.score-perfect { background: #dcfce7; color: #166534; }
.status-text { font-weight: 600; font-size: 0.75rem; }
.status-text.success { color: var(--success); }
.status-text.warning { color: var(--danger); }
.progress-container { height: 6px; background: #f1f5f9; border-radius: 99px; width: 100%; overflow: hidden; }
.progress-bar { height: 100%; background: var(--primary); }
.status-badge { padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.75rem; font-weight: 500; border: 1px solid transparent; display: inline-block; white-space: nowrap; }
.status-badge.success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
.status-badge.neutral { background: #fffbeb; color: #b45309; border-color: #fde68a; }

/* Goal Card */
.goal-card-simple { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; max-width: 200px; float: right; text-align: left; }
.goal-top { margin-bottom: 4px; }
.goal-time { font-weight: 600; font-size: 0.75rem; display: flex; align-items: center; gap: 5px; }
.goal-note { font-size: 0.75rem; color: #64748b; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.goal-links { font-size: 0.7rem; display: flex; gap: 8px; align-items: center; }
.link-edit { color: var(--primary); text-decoration: none; font-weight: 500; }
.link-remove { color: var(--danger); background: none; border: none; padding: 0; cursor: pointer; font-size: 0.7rem; font-weight: 500; }
.link-sep { color: #cbd5e1; }
.text-danger { color: var(--danger); }
.text-warning { color: var(--warning); }
.text-primary { color: var(--primary); }
.btn-set-goal { background: var(--primary); color: white; text-decoration: none; padding: 0.4rem 0.85rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500; display: inline-block; white-space: nowrap; transition: background 0.2s; }
.btn-set-goal:hover { background: #4338ca; }

@media (max-width: 768px) {
    .kpi-row, .chart-row { grid-template-columns: 1fr; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    .goal-card-simple { float: none; max-width: 100%; margin-top: 10px; }
    .modal-card { padding: 25px; width: 95%; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

// modal js 
function openSuccessModal(message) {
    document.getElementById('success-message-text').innerText = message;
    document.getElementById('success-modal').style.display = 'flex';
}
function closeSuccessModal() { document.getElementById('success-modal').style.display = 'none'; }
function openConfirmModal(callback) {
    window.proceedWithRemoval = callback;
    document.getElementById('confirmation-modal').style.display = 'flex';
}
function closeConfirmModal() { document.getElementById('confirmation-modal').style.display = 'none'; }

// charts js
Chart.defaults.font.family = "'Inter', sans-serif";

new Chart(document.getElementById('progressChart'), {
    type: 'bar',
    data: {
        labels: @json($analytics->pluck('course_title')),
        datasets: [{
            data: @json($analytics->pluck('progress')),
            backgroundColor: '#4f46e5'
        }]
    },
    options: {
        scales: { y: { beginAtZero: true, max: 100 } },
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('quizChart'), {
    type: 'doughnut',
    data: {
        labels: ['Passed', 'Retake'],
        datasets: [{
            data: [{{ $passedCount }}, {{ $failedCount }}],
            backgroundColor: ['#10b981', '#ef4444']
        }]
    },
    options: { cutout: '70%' }
});
</script>
@endpush
