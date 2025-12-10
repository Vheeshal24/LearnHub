<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourseActivity;
use App\Models\FeaturedRecommendation;
use App\Models\RecommendationSetting;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class RecommendationDashboardController extends Controller
{
    public function __construct(private RecommendationService $service) {}

    public function index(Request $request)
    {
        $settings = RecommendationSetting::first();
        $days = (int) ($settings->default_trending_days ?? 7);

        $totalViews = (int) Course::sum('views_count');
        $totalEnrollments = (int) Course::sum('enrollments_count');
        $completionsLast7Days = (int) UserCourseActivity::where('type', 'complete')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();

        $trending = $this->service->getTrendingCourses(10, $days);

        $featured = collect();
        $featuredActiveCourses = collect();
        $featuredTotal = 0;
        $featuredActiveNow = 0;
        if (Schema::hasTable('featured_recommendations')) {
            $featured = FeaturedRecommendation::with(['course', 'user'])
                ->orderByDesc('created_at')
                ->get();

            $now = Carbon::now();
            $activeQuery = FeaturedRecommendation::query()
                ->where('active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                });

            $featuredActiveNow = (int) $activeQuery->count();
            $featuredActiveCourses = $activeQuery->with('course')->get()->pluck('course');
            $featuredTotal = (int) FeaturedRecommendation::count();
        }

        $recentActivities = UserCourseActivity::orderByDesc('created_at')->limit(20)->get();
        $latestCourses = Course::orderByDesc('published_at')->orderByDesc('created_at')->limit(10)->get();

        // Analytics overview metrics
        $recommendedCoursesUnion = $featuredActiveCourses->merge($trending)->unique('id');
        $totalRecommendationsCurrent = (int) $recommendedCoursesUnion->count();

        $personalizedUsersLastNDays = (int) UserCourseActivity::where('created_at', '>=', Carbon::now()->subDays($days))
            ->select('user_id')
            ->distinct()
            ->count('user_id');

        $recommendedCourseIds = $recommendedCoursesUnion->pluck('id');
        $viewsOnRecommended = (int) UserCourseActivity::whereIn('course_id', $recommendedCourseIds)->where('type', 'view')->count();
        $enrollsOnRecommended = (int) UserCourseActivity::whereIn('course_id', $recommendedCourseIds)->where('type', 'enroll')->count();
        $avgRatingOnRecommended = round((float) UserCourseActivity::whereIn('course_id', $recommendedCourseIds)->whereNotNull('rating')->avg('rating'), 2);
        $engagementRate = $viewsOnRecommended > 0 ? round(($enrollsOnRecommended / $viewsOnRecommended) * 100, 1) : 0.0;

        // Trending tags (top 10)
        $tagCounts = [];
        foreach ($trending as $course) {
            foreach ($course->tags_array as $t) {
                $tagCounts[$t] = ($tagCounts[$t] ?? 0) + 1;
            }
        }
        arsort($tagCounts);
        $trendingTags = collect($tagCounts)->take(10);

        // Most recommended courses list (featured first by priority, then trending order)
        $mostRecommendedCourses = $featuredActiveCourses->merge($trending)->unique('id')->take(10);

        return view('admin.recommendations.dashboard', [
            'totalViews' => $totalViews,
            'totalEnrollments' => $totalEnrollments,
            'completionsLast7Days' => $completionsLast7Days,
            'trending' => $trending,
            'featured' => $featured,
            'recentActivities' => $recentActivities,
            'latestCourses' => $latestCourses,
            'days' => $days,
            // New analytics
            'featuredTotal' => $featuredTotal,
            'featuredActiveNow' => $featuredActiveNow,
            'totalRecommendationsCurrent' => $totalRecommendationsCurrent,
            'personalizedUsersLastNDays' => $personalizedUsersLastNDays,
            'viewsOnRecommended' => $viewsOnRecommended,
            'enrollsOnRecommended' => $enrollsOnRecommended,
            'avgRatingOnRecommended' => $avgRatingOnRecommended,
            'engagementRate' => $engagementRate,
            'trendingTags' => $trendingTags,
            'mostRecommendedCourses' => $mostRecommendedCourses,
        ]);
    }

    public function storeFeatured(Request $request)
    {
        $courseId = $request->integer('course_id');
        if (!$courseId && $request->filled('course_slug')) {
            $slug = $request->string('course_slug')->toString();
            $course = Course::where('slug', $slug)->first();
            if ($course) {
                $courseId = $course->id;
            }
        }
        $note = $request->string('note')->toString();

        if (!$courseId) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        if (!Schema::hasTable('featured_recommendations')) {
            return redirect()->back()->with('error', 'Featured recommendations table not found. Run migrations.');
        }

        $exists = FeaturedRecommendation::where('course_id', $courseId)->exists();
        if (!$exists) {
            FeaturedRecommendation::create([
                'course_id' => $courseId,
                'note' => $note,
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('status', 'Featured course added.');
    }

    public function destroyFeatured(FeaturedRecommendation $featured)
    {
        if (!Schema::hasTable('featured_recommendations')) {
            return redirect()->back()->with('error', 'Featured recommendations table not found.');
        }
        $featured->delete();
        return redirect()->back()->with('status', 'Featured course removed.');
    }

    public function refresh(Request $request)
    {
        Cache::flush();
        return redirect()->back()->with('status', 'Recommendations refreshed.');
    }
}