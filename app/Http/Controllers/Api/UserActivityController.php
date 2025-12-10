<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\UserCourseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_id' => 'nullable|integer|exists:lessons,id',
            'type' => 'required|string|in:view,enroll,complete,rate',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $activity = DB::transaction(function () use ($data) {
            $activity = UserCourseActivity::create($data);

            $course = Course::findOrFail($data['course_id']);

            if ($data['type'] === 'view') {
                $course->increment('views_count');
            }

            if ($data['type'] === 'enroll') {
                $course->increment('enrollments_count');
            }

            if ($data['type'] === 'rate' && isset($data['rating'])) {
                $avg = UserCourseActivity::where('course_id', $course->id)
                    ->where('type', 'rate')
                    ->avg('rating');
                $course->rating = round((float) $avg, 2);
                $course->save();
            }

            return $activity;
        });

        return response()->json($activity, 201);
    }
}