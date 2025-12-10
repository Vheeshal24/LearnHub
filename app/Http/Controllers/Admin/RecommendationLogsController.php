<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedRecommendation;
use App\Models\RecommendationSetting;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationLogsController extends Controller
{
    public function index(Request $request, RecommendationService $service)
    {
        $days = (int) $request->input('days', 30);
        $limit = (int) $request->input('limit', 20);

        $manual = FeaturedRecommendation::with('course', 'user')
            ->orderByDesc('created_at')
            ->paginate(15, ['*'], 'manual_page');

        $trending = $service->getTrendingCourses($limit, $days);

        $settings = RecommendationSetting::first();
        $viewsW = (float) ($settings->views_weight ?? 1.0);
        $enrollW = (float) ($settings->enrollments_weight ?? 2.0);
        $activityW = (float) ($settings->activity_weight ?? 1.0);

        $system = $trending->map(function ($course) use ($viewsW, $enrollW, $activityW) {
            $score = $activityW * ((int) ($course->recent_activity_count ?? 0))
                + $viewsW * ((int) $course->views_count)
                + $enrollW * ((int) $course->enrollments_count);
            return [
                'course' => null,
                'recommended' => $course,
                'score' => round($score, 2),
                'created_at' => $course->created_at,
                'type' => 'System',
            ];
        });

        return view('admin.recommendations.logs.index', [
            'manual' => $manual,
            'system' => $system,
            'days' => $days,
            'limit' => $limit,
        ]);
    }
}