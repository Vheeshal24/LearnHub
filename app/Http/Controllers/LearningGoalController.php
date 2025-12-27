<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LearningGoal;
use App\Models\Course;
use App\Models\UserCourseActivity; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LearningGoalController extends Controller
{
   public function create(Request $request)
    {
        $selectedCourse = null;

        if ($request->filled('course_id')) {
            $selectedCourse = Course::find((int) $request->course_id);
        }

        return view('learning_goals.create', compact('selectedCourse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'target_date' => 'required|date|after_or_equal:today',
            'note'        => 'required|string|max:255',
        ]);
        $deadline = Carbon::parse($request->target_date)->endOfDay();

        LearningGoal::updateOrCreate(
            ['user_id' => Auth::id(), 'course_id' => $request->course_id],
            [
                'goal_description'       => $request->note,
                'target_completion_time' => $deadline, 
                'start_date'             => Carbon::now(),
                'target_lessons'         => 0,
            ]
        );

        return redirect()->route('analytics.dashboard')->with('success', 'Goal set successfully!');
    }
    public function edit($id)
    {
        $goal = LearningGoal::findOrFail($id);
        if ($goal->user_id !== Auth::id()) abort(403);
        return view('learning_goals.edit', compact('goal'));
    }
    
    public function update(Request $request, $id)
    {
        $goal = LearningGoal::findOrFail($id);
        if ($goal->user_id !== Auth::id()) abort(403);

        $request->validate([
            'target_date' => 'required|date|after_or_equal:today',
            'note'        => 'required|string|max:255',
        ]);

        $deadline = Carbon::parse($request->target_date)->endOfDay();

        $goal->update([
            'goal_description'       => $request->note,
            'target_completion_time' => $deadline,
        ]);

        return redirect()->route('analytics.dashboard')->with('success', 'Goal updated successfully!');
    }

    //delete
    public function destroy(LearningGoal $goal)
    {
        if ($goal->user_id !== Auth::id()) abort(403);
        
        $goal->delete();
        
        return back()->with('success', 'Goal removed.');
    }
}