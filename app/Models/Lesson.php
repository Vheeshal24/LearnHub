<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'slug', 'description', 'content_url', 'material_file', 'duration_minutes', 'position', 'published', 'quiz_json',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . substr(md5(uniqid('', true)), 0, 6);
            }
        });
    }
}