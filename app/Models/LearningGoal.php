<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningGoal extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'course_id',
        'goal_description',       
        'target_completion_time', 
        'start_date',
        'target_days',
        'target_lessons',
        'completed_lessons',
        'status', 
    ];

    //goal timer
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_completion_time' => 'datetime', 
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}