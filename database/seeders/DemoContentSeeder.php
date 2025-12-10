<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'AI Fundamentals',
                'category' => 'ai',
                'tags' => 'ai,ml,beginner',
                'description' => 'Learn the basics of artificial intelligence concepts and terminology.',
                'published_at' => now()->subDays(30),
                'views_count' => 120,
                'enrollments_count' => 45,
                'rating' => 4.4,
                'lessons' => [
                    ['title' => 'Introduction to AI', 'description' => 'Overview of AI history and applications', 'duration_minutes' => 12, 'content_url' => 'https://example.com/ai/intro'],
                    ['title' => 'AI vs ML vs DL', 'description' => 'Differences and relationships', 'duration_minutes' => 18, 'content_url' => 'https://example.com/ai/terms'],
                    ['title' => 'Ethics in AI', 'description' => 'Responsible AI principles', 'duration_minutes' => 15, 'content_url' => 'https://example.com/ai/ethics'],
                    ['title' => 'Future of AI', 'description' => 'Trends and possibilities', 'duration_minutes' => 20, 'content_url' => 'https://example.com/ai/future'],
                ],
            ],
            [
                'title' => 'Machine Learning 101',
                'category' => 'ai',
                'tags' => 'ml,supervised,unsupervised',
                'description' => 'Build intuition for core ML techniques and workflows.',
                'published_at' => now()->subDays(20),
                'views_count' => 220,
                'enrollments_count' => 80,
                'rating' => 4.6,
                'lessons' => [
                    ['title' => 'ML Overview', 'description' => 'What is ML and why it matters', 'duration_minutes' => 14, 'content_url' => 'https://example.com/ml/overview'],
                    ['title' => 'Supervised Learning', 'description' => 'Regression and classification', 'duration_minutes' => 22, 'content_url' => 'https://example.com/ml/supervised'],
                    ['title' => 'Unsupervised Learning', 'description' => 'Clustering and dimensionality reduction', 'duration_minutes' => 19, 'content_url' => 'https://example.com/ml/unsupervised'],
                    ['title' => 'Model Evaluation', 'description' => 'Metrics and validation strategies', 'duration_minutes' => 17, 'content_url' => 'https://example.com/ml/evaluation'],
                ],
            ],
            [
                'title' => 'Laravel Web Development',
                'category' => 'web',
                'tags' => 'php,laravel,backend',
                'description' => 'Build modern web backends using Laravel.',
                'published_at' => now()->subDays(10),
                'views_count' => 300,
                'enrollments_count' => 150,
                'rating' => 4.7,
                'lessons' => [
                    ['title' => 'Getting Started', 'description' => 'Install and configure a Laravel app', 'duration_minutes' => 25, 'content_url' => 'https://example.com/laravel/start'],
                    ['title' => 'Routing Essentials', 'description' => 'Define routes and controllers', 'duration_minutes' => 20, 'content_url' => 'https://example.com/laravel/routing'],
                    ['title' => 'Eloquent Models', 'description' => 'Work with the database using Eloquent', 'duration_minutes' => 28, 'content_url' => 'https://example.com/laravel/eloquent'],
                    ['title' => 'APIs with Laravel', 'description' => 'Build RESTful endpoints', 'duration_minutes' => 24, 'content_url' => 'https://example.com/laravel/apis'],
                ],
            ],
            [
                'title' => 'Data Science Bootcamp',
                'category' => 'data',
                'tags' => 'python,data,analysis',
                'description' => 'From data cleaning to visualization and inference.',
                'published_at' => now()->subDays(5),
                'views_count' => 180,
                'enrollments_count' => 70,
                'rating' => 4.5,
                'lessons' => [
                    ['title' => 'Data Cleaning', 'description' => 'Tidy data and handling missing values', 'duration_minutes' => 26, 'content_url' => 'https://example.com/data/cleaning'],
                    ['title' => 'Exploratory Analysis', 'description' => 'EDA techniques and plots', 'duration_minutes' => 23, 'content_url' => 'https://example.com/data/eda'],
                    ['title' => 'Visualization', 'description' => 'Communicating insights visually', 'duration_minutes' => 21, 'content_url' => 'https://example.com/data/viz'],
                    ['title' => 'Statistical Inference', 'description' => 'Hypothesis testing basics', 'duration_minutes' => 29, 'content_url' => 'https://example.com/data/inference'],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $lessons = $courseData['lessons'];
            unset($courseData['lessons']);

            // Slug is auto-generated in model boot if empty
            $course = Course::create($courseData);

            $position = 1;
            foreach ($lessons as $lessonData) {
                $lessonData['course_id'] = $course->id;
                $lessonData['position'] = $position++;
                // Slug auto-generated in model boot
                Lesson::create($lessonData);
            }
        }
    }
}