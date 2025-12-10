<?php

use App\Models\Course;
use App\Models\User;

it('tracks views, enrollments, and ratings', function () {
    $user = User::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'secret']);
    $course = Course::create([
        'title' => 'Testing 101',
        'category' => 'QA',
        'tags' => 'testing,qa',
        'description' => 'Basics of testing',
    ]);

    // View
    $viewRes = $this->postJson('/api/user-activity', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'type' => 'view',
    ]);
    $viewRes->assertCreated();
    $course->refresh();
    expect($course->views_count)->toBe(1);

    // Enroll
    $enrollRes = $this->postJson('/api/user-activity', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'type' => 'enroll',
    ]);
    $enrollRes->assertCreated();
    $course->refresh();
    expect($course->enrollments_count)->toBe(1);

    // Rate
    $rateRes = $this->postJson('/api/user-activity', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'type' => 'rate',
        'rating' => 4,
    ]);
    $rateRes->assertCreated();
    $course->refresh();
    expect($course->rating)->toBe(4.0);
});