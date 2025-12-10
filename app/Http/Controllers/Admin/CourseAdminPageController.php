<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseAdminPageController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->orderByDesc('created_at');
        $search = trim((string) $request->input('q'));

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(12)->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('admin.courses.create');
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

        return redirect()->route('admin.courses.index')->with('status', 'Course created successfully');
    }

    public function edit(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        return view('admin.courses.edit', ['course' => $course]);
    }

    public function update(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();

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

        return redirect()->route('admin.courses.index')->with('status', 'Course updated successfully');
    }

    public function destroy(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted successfully');
    }
}