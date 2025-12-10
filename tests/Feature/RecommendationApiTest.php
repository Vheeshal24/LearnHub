<?php

use App\Models\Course;
use App\Models\User;

it('returns trending, related, and personalized recommendations', function () {
    $user = User::create(['name' => 'Bob', 'email' => 'bob@example.com', 'password' => 'secret']);

    $courseA = Course::create([
        'title' => 'Laravel Intro',
        'category' => 'Backend',
        'tags' => 'php,laravel,intro',
        'description' => 'Start here',
    ]);

    $courseB = Course::create([
        'title' => 'Laravel Advanced',
        'category' => 'Backend',
        'tags' => 'php,laravel,advanced',
        'description' => 'Deep dive',
    ]);

    // Engage with A to drive personalization and trending
    $this->postJson('/api/user-activity', [
        'user_id' => $user->id,
        'course_id' => $courseA->id,
        'type' => 'view',
    ])->assertCreated();

    $this->postJson('/api/user-activity', [
        'user_id' => $user->id,
        'course_id' => $courseA->id,
        'type' => 'enroll',
    ])->assertCreated();

    // Trending should rank A ahead of B
    $trending = $this->getJson('/api/recommendations/trending/courses?days=14&limit=5');
    $trending->assertOk();
    $ids = collect($trending->json())->pluck('id')->toArray();
    expect($ids[0])->toBe($courseA->id);

    // Related to A should include B (same category/tags overlap)
    $related = $this->getJson('/api/recommendations/related/courses?course_id='.$courseA->id.'&limit=5');
    $related->assertOk();
    $relatedIds = collect($related->json())->pluck('id')->toArray();
    expect($relatedIds)->toContain($courseB->id);

    // Personalized for user should suggest B (not engaged but matches preferences)
    $personalized = $this->getJson('/api/recommendations/personalized/courses?user_id='.$user->id.'&limit=5');
    $personalized->assertOk();
    $personalizedIds = collect($personalized->json())->pluck('id')->toArray();
    expect($personalizedIds)->toContain($courseB->id);
});