<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'note', 'created_by', 'active', 'priority', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'priority' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}