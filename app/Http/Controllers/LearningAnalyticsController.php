<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LessonQuizAttempt;
use Illuminate\Support\Facades\DB;
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
        $rows = LessonQuizAttempt::query()
            ->join('lessons', 'lessons.id', '=', 'lesson_quiz_attempts.lesson_id')
            ->join('courses', 'courses.id', '=', 'lessons.course_id')
            ->where('lesson_quiz_attempts.user_id', $user->id)
            ->selectRaw("
                courses.id as course_id,
                courses.title as course_title,
                COUNT(*) as quiz_attempts,
                SUM(CASE WHEN lesson_quiz_attempts.score >= lesson_quiz_attempts.total THEN 1 ELSE 0 END) as completed_lessons
            ")
            ->groupBy('courses.id', 'courses.title')
            ->get();

        return collect($rows)->map(function ($row) {
            $total = (int) $row->quiz_attempts;
            $completed = (int) $row->completed_lessons;
            $progress = $total ? (int) round(($completed / $total) * 100) : 0;
            $status = $progress === 100 ? 'Completed' : 'Retake';
            return [
                'course_id' => (int) $row->course_id,
                'course_title' => $row->course_title,
                'completed_lessons' => $completed,
                'total_lessons' => $total,
                'quiz_attempts' => $total,
                'progress' => $progress,
                'status' => $status,
            ];
        });
    }

   public function dashboard(Request $request)
    {
        $user = Auth::user();
        $analytics = $this->getAnalyticsData($user);

        $quizQuery = LessonQuizAttempt::query()
            ->with([
                'lesson:id,title,course_id',
                'lesson.course:id,title'
            ])
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

        $quizHistory = $quizQuery
            ->limit(200)
            ->get()
            ->map(function ($quiz) {
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

        $courses = Course::select('id', 'title')->orderBy('title')->get();

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

        $maxRows = (int) env('ANALYTICS_PDF_MAX_ROWS', 500);
        if ($analytics->count() > $maxRows) {
            $analytics = $analytics->take($maxRows);
            $pdf = Pdf::loadView(
                'learning_analytics.export_pdf',
                compact('analytics', 'user', 'reportType')
            );
        }

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
