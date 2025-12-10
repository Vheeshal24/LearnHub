<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FeaturedRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeaturedRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $query = FeaturedRecommendation::query()->with(['course','user'])->orderByDesc('priority')->orderByDesc('created_at');
        $active = $request->input('active');
        $search = trim((string) $request->input('q'));

        if ($active === '1') {
            $query->where('active', true);
        } elseif ($active === '0') {
            $query->where('active', false);
        }

        if (!empty($search)) {
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(12)->withQueryString();

        return view('admin.recommendations.featured.index', [
            'items' => $items,
            'search' => $search,
            'active' => $active,
        ]);
    }

    public function create(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $coursesQuery = Course::query()->orderByDesc('created_at');
        if (!empty($search)) {
            $coursesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }
        $courses = $coursesQuery->limit(20)->get();

        return view('admin.recommendations.featured.create', [
            'courses' => $courses,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required','integer','exists:courses,id'],
            'note' => ['nullable','string','max:255'],
            'active' => ['nullable','boolean'],
            'priority' => ['nullable','integer','min:0','max:100'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
        ]);

        $already = FeaturedRecommendation::where('course_id', $data['course_id'])->first();
        if ($already) {
            return back()->with('error', 'This course is already featured').withInput();
        }

        $data['active'] = (bool) ($data['active'] ?? true);
        $data['priority'] = (int) ($data['priority'] ?? 0);
        $data['created_by'] = Auth::id();

        FeaturedRecommendation::create($data);

        return redirect()->route('admin.recommendations.featured.index')->with('status', 'Featured recommendation created');
    }

    public function edit(FeaturedRecommendation $featured)
    {
        return view('admin.recommendations.featured.edit', [
            'featured' => $featured->load('course','user'),
        ]);
    }

    public function update(Request $request, FeaturedRecommendation $featured)
    {
        $data = $request->validate([
            'course_id' => ['nullable','integer','exists:courses,id'],
            'note' => ['nullable','string','max:255'],
            'active' => ['nullable','boolean'],
            'priority' => ['nullable','integer','min:0','max:100'],
            'starts_at' => ['nullable','date'],
            'ends_at' => ['nullable','date','after_or_equal:starts_at'],
        ]);

        if (!empty($data['course_id']) && $data['course_id'] != $featured->course_id) {
            $exists = FeaturedRecommendation::where('course_id', $data['course_id'])->exists();
            if ($exists) {
                return back()->with('error', 'Another featured item already selects this course').withInput();
            }
        }

        $featured->update($data);

        return redirect()->route('admin.recommendations.featured.index')->with('status', 'Featured recommendation updated');
    }

    public function destroy(FeaturedRecommendation $featured)
    {
        $featured->delete();
        return redirect()->route('admin.recommendations.featured.index')->with('status', 'Featured recommendation deleted');
    }
}