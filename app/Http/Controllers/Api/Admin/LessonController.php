<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        return response()->json($course->lessons()->orderBy('position')->paginate($request->integer('per_page', 20)));
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'duration_minutes' => 'nullable|integer|min:0',
            'position' => 'nullable|integer|min:0',
            'published' => 'nullable|boolean',
        ]);

        $data['course_id'] = $course->id;
        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }

    public function show(Lesson $lesson)
    {
        return response()->json($lesson);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'duration_minutes' => 'nullable|integer|min:0',
            'position' => 'nullable|integer|min:0',
            'published' => 'nullable|boolean',
        ]);

        $lesson->update($data);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(['deleted' => true]);
    }
}