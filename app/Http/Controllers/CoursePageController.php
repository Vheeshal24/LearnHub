<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationService;

class CoursePageController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}

    public function show(string $slug, Request $request)
    {
        $course = Course::with(['lessons' => function ($q) {
            $q->orderBy('position');
        }])->where('slug', $slug)->firstOrFail();

        $relatedCourses = $this->recommendationService->getRelatedCourses($course, 4);
        
        if (Auth::check()) {
            $completedIds = UserCourseActivity::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('type', 'complete')
                ->pluck('lesson_id')
                ->all();
        } else {
            $progressKey = "progress.course.{$course->id}.completed_lessons";
            $completedIds = (array) $request->session()->get($progressKey, []);
        }
        $totalCount = max(1, $course->lessons->count());
        $completedCount = count(array_intersect($completedIds, $course->lessons->pluck('id')->all()));
        $progressPercent = (int) round(($completedCount / $totalCount) * 100);

        return view('course.show', [
            'course' => $course,
            'relatedCourses' => $relatedCourses,
            'completedIds' => $completedIds,
            'completedCount' => $completedCount,
            'totalCount' => $totalCount,
            'progressPercent' => $progressPercent,
        ]);
    }

    public function enroll(string $slug, Request $request)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

        $course->increment('enrollments_count');

        if (Auth::check()) {
            UserCourseActivity::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'lesson_id' => null,
                'type' => 'enroll',
                'rating' => null,
            ]);
        } else {
            $request->session()->put("progress.course.{$course->id}.enrolled", true);
        }

        return redirect()->route('courses.show', $slug)->with('status', 'Enrolled in ' . $course->title);
    }
}