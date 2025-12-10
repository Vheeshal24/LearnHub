<?php

use App\Models\Course;

it('allows admin to CRUD courses', function () {
    // Create
    $create = $this->postJson('/api/admin/courses', [
        'title' => 'Laravel Basics',
        'description' => 'Intro course',
        'category' => 'Backend',
        'tags' => ['laravel','php'],
        'published_at' => now()->toDateTimeString(),
    ]);
    $create->assertCreated();
    $courseId = $create->json('id');
    expect($courseId)->toBeInt();

    // Show
    $show = $this->getJson("/api/admin/courses/{$courseId}");
    $show->assertOk();
    expect($show->json('title'))->toBe('Laravel Basics');

    // Update
    $update = $this->putJson("/api/admin/courses/{$courseId}", [
        'title' => 'Laravel Advanced',
        'tags' => ['laravel','advanced'],
    ]);
    $update->assertOk();
    expect($update->json('title'))->toBe('Laravel Advanced');

    // Delete
    $delete = $this->deleteJson("/api/admin/courses/{$courseId}");
    $delete->assertOk();

    // Verify deletion
    $this->getJson("/api/admin/courses/{$courseId}")->assertNotFound();
});