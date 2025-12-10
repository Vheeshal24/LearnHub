<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserCourseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonPageController extends Controller
{
    public function show(string $courseSlug, string $lessonSlug, Request $request)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('slug', $lessonSlug)->where('course_id', $course->id)->firstOrFail();

        $lessons = $course->lessons; // ordered by position via relation

        // Determine previous and next lessons by position
        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex !== false && $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex !== false && $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        // Session-based progress for this course
        // Progress: use persistent activities if logged in, fallback to session
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
        $isCompleted = in_array($lesson->id, $completedIds, true);

        return view('lesson.show', [
            'course' => $course,
            'lesson' => $lesson,
            'lessons' => $lessons,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'isCompleted' => $isCompleted,
            'completedIds' => $completedIds,
        ]);
    }

    public function complete(string $courseSlug, string $lessonSlug, Request $request)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('slug', $lessonSlug)->where('course_id', $course->id)->firstOrFail();

        if (Auth::check()) {
            $exists = UserCourseActivity::where([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'type' => 'complete',
            ])->exists();

            if (!$exists) {
                UserCourseActivity::create([
                    'user_id' => Auth::id(),
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id,
                    'type' => 'complete',
                ]);
            }
        }

        $progressKey = "progress.course.{$course->id}.completed_lessons";
        $completedIds = (array) $request->session()->get($progressKey, []);

        if (!in_array($lesson->id, $completedIds, true)) {
            $completedIds[] = $lesson->id;
            $request->session()->put($progressKey, $completedIds);
        }

        return redirect()->route('lessons.show', [$course->slug, $lesson->slug])
            ->with('status', 'Lesson marked as completed');
    }
}