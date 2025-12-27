@extends('layouts.app')

@section('title', 'Set Learning Goal')

@section('content')
<div class="container" style="max-width: 550px; padding-top: 40px; padding-bottom: 40px;">
    
    <div class="card">
        {{-- Header --}}
        <div class="card-header" style="background: white; border-bottom: 1px solid #e5e7eb; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; color: #1f2937; font-weight: 600;font-weight-bold;">Set Learning Goal</h3>
                    <p style="font-size: 0.85rem;font-weight-bold; color: #6b7280; margin-top: 10px;">Define a completion deadline for this course.</p>
                </div>
            </div>
        </div>

        <div class="card-body" style="padding: 25px;">
            <form action="{{ route('learning_goals.store') }}" method="POST">
                @csrf
                <div class="form-group mb-4">
                    <label class="input-label">Course</label>
                    <div class="input-wrapper" style="position: relative;">
                        <input type="text" 
                               class="form-control" 
                               value="{{ $selectedCourse->title ?? 'Select a course from Dashboard' }}" 
                               disabled 
                               style="background-color: #f3f4f6; color: #4b5563; cursor: not-allowed; font-weight: 600;">
                        <i class="fas fa-lock" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 12px;"></i>
                    </div>
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id ?? '' }}">
                </div>

                {{-- Date--}}
                <div class="form-group mb-4">
                    <label class="input-label"style="margin-top: 15px;">Target Completion Date</label>
                    <div class="input-wrapper">
                        <input type="date" 
                               name="target_date" 
                               class="form-control" 
                               min="{{ date('Y-m-d') }}" 
                               required
                               style="height: 50px; font-weight: 600; font-size: 1.1rem; color: #4f46e5;">
                    </div>

                </div>
                {{-- Goal --}}
                <div class="form-group mb-4">
                    <label class="input-label"style="margin-top: 15px;">Goal Description</label>
                    <textarea name="note" class="form-control custom-textarea" rows="2" placeholder="e.g. Complete all quizzes and final project..." required style="margin-bottom: 15px;"></textarea>
                </div>
                {{-- Actions --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-block start-btn">
                        Set Goal
                    </button>
                    <a href="{{ route('analytics.dashboard') }}" class="btn-cancel">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Professional Theme Variables */
    :root {
        --border: #e5e7eb;
        --muted: #6b7280;
        --primary: #4f46e5;
        --bg-light: #f9fafb;
    }

    /* Clean Card Styling */
    .card {
        border: 1px solid var(--border);
        border-radius: 8px;
        background: white;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .icon-header {
        width: 40px;
        height: 40px;
        background: #eef2ff; /* Indigo-50 */
        color: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Form Typography */
    .input-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151; /* Gray-700 */
        margin-bottom: 6px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Inputs */
    .form-control {
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 10px;
        font-size: 0.95rem;
        color: #1f2937;
        width: 100%;
        background-color: white;
    }
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 1px var(--primary);
    }

    .custom-textarea { resize: none; background-color: var(--bg-light); }

    /* Info Box */
    .info-box {
        background: var(--bg-light);
        border: 1px solid var(--border);
        padding: 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: var(--muted);
        display: flex;
        align-items: center;
    }

    /* Buttons */
    .start-btn {
        background-color: var(--primary);
        color: white;
        border: none;
        padding: 12px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        margin-bottom: 10px;
    }
    .start-btn:hover { background-color: #4338ca; }

    .btn-cancel {
        display: block;
        text-align: center;
        color: var(--muted);
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-cancel:hover { color: #374151; }
</style>
@endpush