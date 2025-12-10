<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(private RecommendationService $service)
    {
    }

    public function personalizedCourses(Request $request)
    {
        $userId = $request->integer('user_id');
        $limit = $request->integer('limit', 10);

        $user = User::findOrFail($userId);
        $courses = $this->service->getPersonalizedCourses($user, $limit);

        return response()->json($courses);
    }

    public function personalizedLessons(Request $request)
    {
        $userId = $request->integer('user_id');
        $limit = $request->integer('limit', 10);

        $user = User::findOrFail($userId);
        $lessons = $this->service->getPersonalizedLessons($user, $limit);

        return response()->json($lessons);
    }

    public function relatedCourses(Request $request)
    {
        $courseId = $request->integer('course_id');
        $limit = $request->integer('limit', 10);

        $course = Course::findOrFail($courseId);
        $related = $this->service->getRelatedCourses($course, $limit);

        return response()->json($related);
    }

    public function trendingCourses(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $days = $request->integer('days', 7);

        $trending = $this->service->getTrendingCourses($limit, $days);

        return response()->json($trending);
    }
}