<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonQuizAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LearningGoal;
use Carbon\Carbon;


class LearningAnalyticsController extends Controller
{
    // rertieve quiz data
    private function getAnalyticsData($user)
    {
        $attempts = LessonQuizAttempt::with('lesson.course')
            ->where('user_id', $user->id)
            ->get();

        $coursesData = [];

        foreach ($attempts as $attempt) {
            $lesson = $attempt->lesson;
            $course = $lesson->course;
            $courseId = $course->id;

            if (!isset($coursesData[$courseId])) {
                $coursesData[$courseId] = [
                    'course_id' => $courseId,
                    'course_title' => $course->title,
                    'completed_lessons' => 0,
                    'total_lessons' => 0,
                    'quiz_attempts' => 0,
                ];
            }

            // Count lesson attempt
            $coursesData[$courseId]['total_lessons']++;
            $coursesData[$courseId]['quiz_attempts']++;

            // Check completed/not
            if ($attempt->score >= $attempt->total) {
                $coursesData[$courseId]['completed_lessons']++;
            }
        }

        // logic calculate progress and status
        foreach ($coursesData as &$course) {
            $course['progress'] = $course['total_lessons']
                ? round(($course['completed_lessons'] / $course['total_lessons']) * 100)
                : 0;

            $course['status'] = ($course['progress'] == 100)
                ? 'Completed'
                : 'Retake';
        }

        return collect(array_values($coursesData));
    }

   public function dashboard(Request $request)
    {
        $user = Auth::user();
        $analytics = $this->getAnalyticsData($user);

        $quizQuery = LessonQuizAttempt::query()
            ->with('lesson.course')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('course_id')) {
            $analytics = $analytics
                ->where('course_id', $request->course_id)
                ->values();

            $quizQuery->whereHas('lesson.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }

        $quizHistory = $quizQuery->get()->map(function ($quiz) {
            $quiz->course_title = $quiz->lesson->course->title;
            $quiz->lesson_title = $quiz->lesson->title;
            $quiz->percentage_score = $quiz->total > 0
                ? round(($quiz->score / $quiz->total) * 100)
                : 0;
            return $quiz;
        });

        $passedCount = $quizHistory->where('percentage_score', '>=', 100)->count();
        $failedCount = $quizHistory->where('percentage_score', '<', 100)->count();

        $totalCoursesEnrolled = $analytics->count();
        $averageProgress = round($analytics->avg('progress'));
        $totalQuizzesTaken = $analytics->sum('quiz_attempts');

        $goals = LearningGoal::where('user_id', $user->id)
            ->get()
            ->keyBy('course_id');

        $courses = Course::all(); 

        return view(
            'learning_analytics.dashboard',
            compact(
                'analytics',
                'quizHistory',
                'totalCoursesEnrolled',
                'averageProgress',
                'totalQuizzesTaken',
                'passedCount',
                'failedCount',
                'goals',
                'courses'
            )
        );
}

    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        $analytics = $this->getAnalyticsData($user);

        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $analytics = $analytics
                ->where('course_id', $request->course_id)
                ->values();

            // Get the actual course title for report
            $course = Course::find($request->course_id);
            $reportType = $course ? $course->title : 'Selected Course';
        } else {
            $reportType = 'All Courses';
        }

        $pdf = Pdf::loadView(
            'learning_analytics.export_pdf',
            compact('analytics', 'user', 'reportType')
        );

        return $pdf->download("learning_report_{$user->id}.pdf");
    }

        public function exportCSV(Request $request)
    {
        $user = Auth::user();
        $analytics = $this->getAnalyticsData($user);
        if ($request->filled('course_id') && $request->course_id !== 'all') {
            $analytics = $analytics
                ->where('course_id', $request->course_id)
                ->values();
        }

        $filename = "learning_report_{$user->id}.csv";

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate",
            "Expires" => "0"
        ];

        $callback = function () use ($analytics) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Course Name',
                'Completed Lessons',
                'Total Lessons',
                'Progress (%)',
                'Number of Quiz Attempts'
            ]);

            foreach ($analytics as $item) {
                fputcsv($file, [
                    $item['course_title'],
                    $item['completed_lessons'],
                    $item['total_lessons'],
                    $item['progress'] . '%', 
                    $item['quiz_attempts']
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
