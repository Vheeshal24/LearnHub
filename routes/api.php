<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\UserActivityController;

Route::prefix('admin')->middleware([\Illuminate\Cookie\Middleware\EncryptCookies::class, \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, \Illuminate\Session\Middleware\StartSession::class, \Illuminate\Auth\Middleware\Authenticate::class, \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/courses', [AdminCourseController::class, 'index']);
    Route::post('/courses', [AdminCourseController::class, 'store']);
    Route::get('/courses/{course}', [AdminCourseController::class, 'show']);
    Route::put('/courses/{course}', [AdminCourseController::class, 'update']);
    Route::patch('/courses/{course}', [AdminCourseController::class, 'update']);
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy']);

    Route::get('/courses/{course}/lessons', [AdminLessonController::class, 'index']);
    Route::post('/courses/{course}/lessons', [AdminLessonController::class, 'store']);
    Route::get('/lessons/{lesson}', [AdminLessonController::class, 'show']);
    Route::put('/lessons/{lesson}', [AdminLessonController::class, 'update']);
    Route::patch('/lessons/{lesson}', [AdminLessonController::class, 'update']);
    Route::delete('/lessons/{lesson}', [AdminLessonController::class, 'destroy']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/stats', [AdminUserController::class, 'stats']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::put('/users/{user}', [AdminUserController::class, 'update']);
    Route::patch('/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
});

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show']);
Route::get('/courses/{course}/lessons', [CourseController::class, 'lessons']);

Route::get('/recommendations/personalized/courses', [RecommendationController::class, 'personalizedCourses']);
Route::get('/recommendations/personalized/lessons', [RecommendationController::class, 'personalizedLessons']);
Route::get('/recommendations/related/courses', [RecommendationController::class, 'relatedCourses']);
Route::get('/recommendations/trending/courses', [RecommendationController::class, 'trendingCourses']);

Route::post('/user-activity', [UserActivityController::class, 'store']);
