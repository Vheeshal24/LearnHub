<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->withCount('lessons');

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('tag')) {
            $query->where('tags', 'LIKE', '%' . $request->string('tag') . '%');
        }

        return response()->json($query->orderByDesc('published_at')->paginate($request->integer('per_page', 15)));
    }

    public function show(Course $course)
    {
        return response()->json($course->load(['lessons']));
    }

    public function lessons(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        return response()->json($course->lessons()->orderBy('position')->paginate($request->integer('per_page', 20)));
    }
}