<?php

namespace App\Models;

use App\Models\UserCourseActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\\Database\\Factories\\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // allow admin creation via registration
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function courseActivities()
    {
        return $this->hasMany(UserCourseActivity::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}
