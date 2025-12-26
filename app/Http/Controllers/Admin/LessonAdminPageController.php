<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonAdminPageController extends Controller
{
    public function index(string $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lessons = $course->lessons; // ordered by relation

        return view('admin.lessons.index', [
            'course' => $course,
            'lessons' => $lessons,
        ]);
    }

    public function create(string $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        return view('admin.lessons.create', ['course' => $course]);
    }

    public function store(Request $request, string $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'duration_minutes' => 'nullable|integer|min:0',
            'position' => 'nullable|integer|min:0',
            'published' => 'nullable|boolean',
            'quiz_json' => 'nullable|string',
            'material_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('material_file')) {
            $data['material_file'] = $request->file('material_file')
                ->store('lesson-materials', 'public');
        }

        $data['course_id'] = $course->id;
        Lesson::create($data);

        return redirect()->route('admin.lessons.index', $course->slug)->with('status', 'Lesson created successfully');
    }

    public function edit(string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('slug', $lessonSlug)->where('course_id', $course->id)->firstOrFail();

        return view('admin.lessons.edit', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    }

    public function update(Request $request, string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('slug', $lessonSlug)->where('course_id', $course->id)->firstOrFail();

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'duration_minutes' => 'nullable|integer|min:0',
            'position' => 'nullable|integer|min:0',
            'published' => 'nullable|boolean',
            'quiz_json' => 'nullable|string',
            'material_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('material_file')) {

            // delete old file
            if ($lesson->material_file) {
                Storage::disk('public')->delete($lesson->material_file);
            }

            // store new file
            $data['material_file'] = $request->file('material_file')
                ->store('lesson-materials', 'public');
        }

        $lesson->update($data);

        return redirect()->route('admin.lessons.index', $course->slug)->with('status', 'Lesson updated successfully');
    }

    public function destroy(string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('slug', $lessonSlug)->where('course_id', $course->id)->firstOrFail();

        $lesson->delete();

        return redirect()->route('admin.lessons.index', $course->slug)->with('status', 'Lesson deleted successfully');
    }
}