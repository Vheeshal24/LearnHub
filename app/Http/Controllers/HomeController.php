<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use App\Models\RecommendationSetting;

class HomeController extends Controller
{
    public function __construct(private RecommendationService $recommendationService) {}
    public function index(Request $request)
    {
        $query = Course::query()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        $category = $request->string('category')->toString();
        $search = trim((string) $request->input('q'));

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(8)->withQueryString();
        $categories = Course::select('category')->distinct()->pluck('category');

        return view('home', [
            'courses' => $courses,
            'categories' => $categories,
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    public function landing(Request $request)
    {
        $featured = Course::orderByDesc('rating')
            ->orderByDesc('views_count')
            ->orderByDesc('published_at')
            ->first();

        $settings = RecommendationSetting::first();
        $days = (int) ($settings->default_trending_days ?? 7);
        $trending = $this->recommendationService->getTrendingCourses(6, $days);

        $newCourses = Course::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $categories = Course::select('category')->distinct()->pluck('category');

        $recommendedCourses = collect();
        $recommendedLessons = collect();
        if (Auth::check()) {
            $recommendedCourses = $this->recommendationService->getPersonalizedCourses(Auth::user(), 6);
            $recommendedLessons = $this->recommendationService->getPersonalizedLessons(Auth::user(), 6);
        }

        return view('home.landing', [
            'featured' => $featured,
            'trending' => $trending,
            'newCourses' => $newCourses,
            'categories' => $categories,
            'recommendedCourses' => $recommendedCourses,
            'recommendedLessons' => $recommendedLessons,
        ]);
    }
}