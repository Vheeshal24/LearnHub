<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserCourseActivity;
use App\Models\RecommendationSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function getTrendingCourses(int $limit = 10, int $days = 7): Collection
    {
        $settings = RecommendationSetting::first();
        $viewsW = (float) ($settings->views_weight ?? 1.0);
        $enrollW = (float) ($settings->enrollments_weight ?? 2.0);
        $activityW = (float) ($settings->activity_weight ?? 1.0);

        $since = Carbon::now()->subDays($days);

        $activityCounts = UserCourseActivity::select('course_id', DB::raw('COUNT(*) as activity_count'))
            ->where('created_at', '>=', $since)
            ->groupBy('course_id');

        return Course::query()
            ->leftJoinSub($activityCounts, 'recent_activity', function ($join) {
                $join->on('courses.id', '=', 'recent_activity.course_id');
            })
            ->select('courses.*', DB::raw('COALESCE(recent_activity.activity_count, 0) as recent_activity_count'))
            ->orderByDesc(DB::raw($activityW . ' * COALESCE(recent_activity.activity_count, 0) + ' . $viewsW . ' * courses.views_count + ' . $enrollW . ' * courses.enrollments_count'))
            ->limit($limit)
            ->get();
    }

    public function getRelatedCourses(Course $course, int $limit = 10): Collection
    {
        $settings = RecommendationSetting::first();
        $viewsW = (float) ($settings->views_weight ?? 1.0);
        $enrollW = (float) ($settings->enrollments_weight ?? 2.0);

        $tags = $course->tags_array;

        $query = Course::query()->where('id', '!=', $course->id);

        if (!empty($course->category)) {
            $query->where('category', $course->category);
        }

        if (!empty($tags)) {
            $query->orWhere(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhere('tags', 'LIKE', '%' . $tag . '%');
                }
            });
        }

        return $query
            ->orderByDesc(DB::raw($viewsW . ' * views_count + ' . $enrollW . ' * enrollments_count'))
            ->limit($limit)
            ->get();
    }

    public function getPersonalizedCourses(User $user, int $limit = 10): Collection
    {
        $settings = RecommendationSetting::first();
        $viewsW = (float) ($settings->views_weight ?? 1.0);
        $enrollW = (float) ($settings->enrollments_weight ?? 2.0);
        $topTagLimit = (int) ($settings->personalized_top_tags_limit ?? 5);

        $activityCourseIds = $user->courseActivities()->pluck('course_id')->unique()->values();
        $topCategories = Course::whereIn('id', $activityCourseIds)
            ->select('category', DB::raw('COUNT(*) as cnt'))
            ->groupBy('category')
            ->orderByDesc('cnt')
            ->pluck('category')
            ->toArray();

        $tagCounts = [];
        Course::whereIn('id', $activityCourseIds)->get()->each(function ($c) use (&$tagCounts) {
            foreach ($c->tags_array as $t) {
                $tagCounts[$t] = ($tagCounts[$t] ?? 0) + 1;
            }
        });
        arsort($tagCounts);
        $topTags = array_keys(array_slice($tagCounts, 0, $topTagLimit));

        $query = Course::query()->whereNotIn('id', $activityCourseIds);

        if (!empty($topCategories)) {
            $query->whereIn('category', $topCategories);
        }

        if (!empty($topTags)) {
            $query->orWhere(function ($q) use ($topTags) {
                foreach ($topTags as $tag) {
                    $q->orWhere('tags', 'LIKE', '%' . $tag . '%');
                }
            });
        }

        return $query
            ->orderByDesc(DB::raw($viewsW . ' * views_count + ' . $enrollW . ' * enrollments_count'))
            ->limit($limit)
            ->get();
    }

    public function getPersonalizedLessons(User $user, int $limit = 10): Collection
    {
        $engagedCourseIds = $user->courseActivities()->pluck('course_id')->unique()->values();
        $completedLessonIds = $user->courseActivities()->where('type', 'complete')->pluck('lesson_id')->filter()->unique()->values();

        return Lesson::query()
            ->whereIn('course_id', $engagedCourseIds)
            ->whereNotIn('id', $completedLessonIds)
            ->orderBy('position')
            ->limit($limit)
            ->get();
    }
}