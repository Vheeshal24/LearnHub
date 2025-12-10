<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\UserCourseActivity;
use App\Models\RecommendationSetting;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}

    public function index(Request $request)
    {
        $user = Auth::user();

        // Personalized
        $personalized = $this->recommendationService->getPersonalizedCourses($user, 8);

        // Continue Learning: recent viewed/enrolled courses, order by latest activity
        $recentCourseIds = UserCourseActivity::where('user_id', $user->id)
            ->whereIn('type', ['view','enroll','complete'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->pluck('course_id')
            ->unique()
            ->take(8)
            ->values();
        $continueCourses = Course::whereIn('id', $recentCourseIds)->with('lessons')->get();

        // Progress per course
        $completedByCourse = UserCourseActivity::where('user_id', $user->id)
            ->where('type', 'complete')
            ->select('course_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('course_id')
            ->pluck('cnt', 'course_id');
        $progress = [];
        foreach ($continueCourses as $c) {
            $total = max(1, $c->lessons->count());
            $done = (int) ($completedByCourse[$c->id] ?? 0);
            $progress[$c->id] = (int) round(($done / $total) * 100);
        }

        // Trending (Top 5 this week from settings)
        $settings = RecommendationSetting::first();
        $days = (int) ($settings->default_trending_days ?? 7);
        $trending = $this->recommendationService->getTrendingCourses(5, $days);

        // Favorites set for quick checks
        $favoriteIds = [];
        if (method_exists($user, 'favorites')) {
            try {
                $favoriteIds = $user->favorites()->pluck('course_id')->toArray();
            } catch (\BadMethodCallException $e) {
                // favorites relationship not defined, leave array empty
            }
        }

        return view('home.dashboard', [
            'user' => $user,
            'personalized' => $personalized,
            'continueCourses' => $continueCourses,
            'progress' => $progress,
            'trending' => $trending,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}