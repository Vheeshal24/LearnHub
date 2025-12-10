<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('tag')) {
            $query->where('tags', 'LIKE', '%' . $request->string('tag') . '%');
        }

        return response()->json($query->orderByDesc('created_at')->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'tags' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        $data['tags'] = is_array($data['tags']) ? implode(',', $data['tags']) : ($data['tags'] ?? null);

        $course = Course::create($data);

        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load('lessons'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|string|max:100',
            'tags' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        if (array_key_exists('tags', $data)) {
            $data['tags'] = is_array($data['tags']) ? implode(',', $data['tags']) : ($data['tags'] ?? null);
        }

        $course->update($data);

        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['deleted' => true]);
    }
}