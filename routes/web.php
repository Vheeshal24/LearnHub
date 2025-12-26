<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CoursePageController;
use App\Http\Controllers\LessonPageController;
use App\Http\Controllers\Admin\CourseAdminPageController;
use App\Http\Controllers\Admin\LessonAdminPageController;
use App\Http\Controllers\Admin\RecommendationSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Admin\RecommendationDashboardController;
use App\Http\Controllers\Admin\RecommendationLogsController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'landing']);
Route::get('/browse', [HomeController::class, 'index'])->name('browse');

Route::get('/courses/{slug}', [CoursePageController::class, 'show'])->name('courses.show');
Route::post('/courses/{slug}/enroll', [CoursePageController::class, 'enroll'])->name('courses.enroll');
Route::get('/courses/{slug}/lessons/{lessonSlug}', [LessonPageController::class, 'show'])->name('lessons.show');
// Add completion endpoint for lessons
Route::post('/courses/{slug}/lessons/{lessonSlug}/complete', [LessonPageController::class, 'complete'])->name('lessons.complete');
Route::post('/courses/{slug}/lessons/{lessonSlug}/quiz', [LessonPageController::class, 'saveQuizAttempt'])->middleware('auth')->name('lessons.quiz.attempt');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/admin/register', [AuthController::class, 'showAdminRegisterForm'])->name('admin.register');
Route::post('/admin/register', [AuthController::class, 'adminRegister'])->name('admin.register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated user dashboard and favorites
Route::middleware([\Illuminate\Auth\Middleware\Authenticate::class])->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/favorites/{course}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware([\Illuminate\Auth\Middleware\Authenticate::class, \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    // Courses CRUD
    Route::get('/courses', [CourseAdminPageController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [CourseAdminPageController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [CourseAdminPageController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{slug}/edit', [CourseAdminPageController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/courses/{slug}', [CourseAdminPageController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{slug}', [CourseAdminPageController::class, 'destroy'])->name('admin.courses.destroy');

    // Lessons CRUD nested under course
    Route::get('/courses/{slug}/lessons', [LessonAdminPageController::class, 'index'])->name('admin.lessons.index');
    Route::get('/courses/{slug}/lessons/create', [LessonAdminPageController::class, 'create'])->name('admin.lessons.create');
    Route::post('/courses/{slug}/lessons', [LessonAdminPageController::class, 'store'])->name('admin.lessons.store');
    Route::get('/courses/{slug}/lessons/{lessonSlug}/edit', [LessonAdminPageController::class, 'edit'])->name('admin.lessons.edit');
    Route::put('/courses/{slug}/lessons/{lessonSlug}', [LessonAdminPageController::class, 'update'])->name('admin.lessons.update');
    Route::delete('/courses/{slug}/lessons/{lessonSlug}', [LessonAdminPageController::class, 'destroy'])->name('admin.lessons.destroy');

    // Recommendation Settings
    Route::get('/recommendations/settings', [RecommendationSettingsController::class, 'edit'])->name('admin.recommendations.settings');
    Route::post('/recommendations/settings', [RecommendationSettingsController::class, 'update'])->name('admin.recommendations.settings.update');

    // Recommendation Dashboard
    Route::get('/recommendations/dashboard', [RecommendationDashboardController::class, 'index'])->name('admin.recommendations.dashboard');
    Route::post('/recommendations/refresh', [RecommendationDashboardController::class, 'refresh'])->name('admin.recommendations.refresh');

    // Featured Recommendations CRUD (separate from course management)
    Route::get('/recommendations/featured', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'index'])->name('admin.recommendations.featured.index');
    Route::get('/recommendations/featured/create', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'create'])->name('admin.recommendations.featured.create');
    Route::post('/recommendations/featured', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'store'])->name('admin.recommendations.featured.store');
    Route::get('/recommendations/featured/{featured}/edit', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'edit'])->name('admin.recommendations.featured.edit');
    Route::put('/recommendations/featured/{featured}', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'update'])->name('admin.recommendations.featured.update');
    Route::delete('/recommendations/featured/{featured}', [\App\Http\Controllers\Admin\FeaturedRecommendationController::class, 'destroy'])->name('admin.recommendations.featured.destroy');

    // Recommendation Logs (read-only view, delete via manual entries)
    Route::get('/recommendations/logs', [RecommendationLogsController::class, 'index'])->name('admin.recommendations.logs.index');

    // Manage Users
    Route::get('/users', [UserAdminController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserAdminController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserAdminController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserAdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserAdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserAdminController::class, 'destroy'])->name('admin.users.destroy');
});
