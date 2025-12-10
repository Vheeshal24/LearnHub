<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationSetting extends Model
{
    protected $fillable = [
        'views_weight',
        'enrollments_weight',
        'activity_weight',
        'default_trending_days',
        'personalized_top_tags_limit',
    ];
}