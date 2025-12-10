<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'category', 'tags',  'published_at', 'views_count', 'enrollments_count', 'rating',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function activities()
    {
        return $this->hasMany(UserCourseActivity::class);
    }

    public function getTagsArrayAttribute(): array
    {
        return array_filter(array_map('trim', explode(',', (string) $this->tags)));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . substr(md5(uniqid('', true)), 0, 6);
            }
        });

        static::updating(function ($model) {
            if (isset($model->title) && empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . substr(md5(uniqid('', true)), 0, 6);
            }
        });
    }
}