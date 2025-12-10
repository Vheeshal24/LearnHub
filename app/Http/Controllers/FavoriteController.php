<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function toggle(Course $course): RedirectResponse
    {
        $user = Auth::user();
        $existing = Favorite::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('status', 'Removed from favorites');
        }

        Favorite::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return back()->with('status', 'Added to favorites');
    }
}