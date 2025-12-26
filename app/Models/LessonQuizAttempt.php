<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonQuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'score',
        'total',
    ];
}