<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insert Courses (preserving IDs and timestamps as requested)
        $courses = [
            [
                'id' => 1, 
                'title' => 'AI Fundamentals', 
                'slug' => 'ai-fundamentals-a939fc', 
                'category' => 'ai', 
                'tags' => 'ai,ml,beginner', 
                'image_url' => NULL, 
                'description' => 'Learn the basics of artificial intelligence concepts and terminology.', 
                'published_at' => '2025-09-19 01:19:52', 
                'views_count' => 120, 
                'enrollments_count' => 45, 
                'rating' => 4.4, 
                'created_at' => '2025-10-19 01:19:52', 
                'updated_at' => '2025-10-19 01:19:52'
            ],
            [
                'id' => 2, 
                'title' => 'Machine Learning 101', 
                'slug' => 'machine-learning-101-2346ac', 
                'category' => 'ai', 
                'tags' => 'ml,supervised,unsupervised', 
                'image_url' => NULL, 
                'description' => 'Build intuition for core ML techniques and workflows.', 
                'published_at' => '2025-09-29 01:19:52', 
                'views_count' => 220, 
                'enrollments_count' => 80, 
                'rating' => 4.6, 
                'created_at' => '2025-10-19 01:19:52', 
                'updated_at' => '2025-10-19 01:19:52'
            ],
            [
                'id' => 3, 
                'title' => 'Laravel Web Development', 
                'slug' => 'laravel-web-development-490fab', 
                'category' => 'web', 
                'tags' => 'php,laravel,backend', 
                'image_url' => NULL, 
                'description' => 'Build modern web backends using Laravel.', 
                'published_at' => '2025-10-09 01:19:52', 
                'views_count' => 300, 
                'enrollments_count' => 150, 
                'rating' => 4.7, 
                'created_at' => '2025-10-19 01:19:52', 
                'updated_at' => '2025-10-19 01:19:52'
            ],
            [
                'id' => 4, 
                'title' => 'Data Science Bootcamp', 
                'slug' => 'data-science-bootcamp-3b4982', 
                'category' => 'data', 
                'tags' => 'python,data,analysis', 
                'image_url' => NULL, 
                'description' => 'From data cleaning to visualization and inference.', 
                'published_at' => '2025-10-14 01:19:52', 
                'views_count' => 180, 
                'enrollments_count' => 70, 
                'rating' => 4.5, 
                'created_at' => '2025-10-19 01:19:52', 
                'updated_at' => '2025-10-19 01:19:52'
            ],
            [
                'id' => 5, 
                'title' => 'AI Fundamentals', 
                'slug' => 'ai-fundamentals-e17000', 
                'category' => 'ai', 
                'tags' => 'ai,ml,beginner', 
                'image_url' => NULL, 
                'description' => 'Learn the basics of artificial intelligence concepts and terminology.', 
                'published_at' => '2025-11-09 07:19:33', 
                'views_count' => 120, 
                'enrollments_count' => 45, 
                'rating' => 4.4, 
                'created_at' => '2025-12-09 07:19:33', 
                'updated_at' => '2025-12-09 07:19:33'
            ],
            [
                'id' => 6, 
                'title' => 'Machine Learning 101', 
                'slug' => 'machine-learning-101-4dfea1', 
                'category' => 'ai', 
                'tags' => 'ml,supervised,unsupervised', 
                'image_url' => NULL, 
                'description' => 'Build intuition for core ML techniques and workflows.', 
                'published_at' => '2025-11-19 07:19:33', 
                'views_count' => 220, 
                'enrollments_count' => 80, 
                'rating' => 4.6, 
                'created_at' => '2025-12-09 07:19:33', 
                'updated_at' => '2025-12-09 07:19:33'
            ],
            [
                'id' => 7, 
                'title' => 'Laravel Web Development', 
                'slug' => 'laravel-web-development-e0213f', 
                'category' => 'web', 
                'tags' => 'php,laravel,backend', 
                'image_url' => NULL, 
                'description' => 'Build modern web backends using Laravel.', 
                'published_at' => '2025-11-29 07:19:33', 
                'views_count' => 300, 
                'enrollments_count' => 150, 
                'rating' => 4.7, 
                'created_at' => '2025-12-09 07:19:33', 
                'updated_at' => '2025-12-09 07:19:33'
            ],
            [
                'id' => 8, 
                'title' => 'Data Science Bootcamp', 
                'slug' => 'data-science-bootcamp-d09ed1', 
                'category' => 'data', 
                'tags' => 'python,data,analysis', 
                'image_url' => NULL, 
                'description' => 'From data cleaning to visualization and inference.', 
                'published_at' => '2025-12-04 07:19:33', 
                'views_count' => 180, 
                'enrollments_count' => 71, 
                'rating' => 4.5, 
                'created_at' => '2025-12-09 07:19:33', 
                'updated_at' => '2025-12-10 21:10:14'
            ],
        ];

        // Using upsert to handle potential duplicates on ID
        DB::table('courses')->upsert($courses, ['id']);

        // 2. Add Lessons for the first 4 courses (matching the original demo content)
        // Course 1: AI
        $this->addLessons(1, [
            ['title' => 'Introduction to AI', 'description' => 'Overview of AI history and applications', 'duration_minutes' => 12, 'content_url' => 'https://example.com/ai/intro'],
            ['title' => 'AI vs ML vs DL', 'description' => 'Differences and relationships', 'duration_minutes' => 18, 'content_url' => 'https://example.com/ai/terms'],
            ['title' => 'Ethics in AI', 'description' => 'Responsible AI principles', 'duration_minutes' => 15, 'content_url' => 'https://example.com/ai/ethics'],
            ['title' => 'Future of AI', 'description' => 'Trends and possibilities', 'duration_minutes' => 20, 'content_url' => 'https://example.com/ai/future'],
        ]);

        // Course 2: ML
        $this->addLessons(2, [
            ['title' => 'ML Overview', 'description' => 'What is ML and why it matters', 'duration_minutes' => 14, 'content_url' => 'https://example.com/ml/overview'],
            ['title' => 'Supervised Learning', 'description' => 'Regression and classification', 'duration_minutes' => 22, 'content_url' => 'https://example.com/ml/supervised'],
            ['title' => 'Unsupervised Learning', 'description' => 'Clustering and dimensionality reduction', 'duration_minutes' => 19, 'content_url' => 'https://example.com/ml/unsupervised'],
            ['title' => 'Model Evaluation', 'description' => 'Metrics and validation strategies', 'duration_minutes' => 17, 'content_url' => 'https://example.com/ml/evaluation'],
        ]);

        // Course 3: Laravel
        $this->addLessons(3, [
            ['title' => 'Getting Started', 'description' => 'Install and configure a Laravel app', 'duration_minutes' => 25, 'content_url' => 'https://example.com/laravel/start'],
            ['title' => 'Routing Essentials', 'description' => 'Define routes and controllers', 'duration_minutes' => 20, 'content_url' => 'https://example.com/laravel/routing'],
            ['title' => 'Eloquent Models', 'description' => 'Work with the database using Eloquent', 'duration_minutes' => 28, 'content_url' => 'https://example.com/laravel/eloquent'],
            ['title' => 'APIs with Laravel', 'description' => 'Build RESTful endpoints', 'duration_minutes' => 24, 'content_url' => 'https://example.com/laravel/apis'],
        ]);

        // Course 4: Data Science
        $this->addLessons(4, [
            ['title' => 'Data Cleaning', 'description' => 'Tidy data and handling missing values', 'duration_minutes' => 26, 'content_url' => 'https://example.com/data/cleaning'],
            ['title' => 'Exploratory Analysis', 'description' => 'EDA techniques and plots', 'duration_minutes' => 23, 'content_url' => 'https://example.com/data/eda'],
            ['title' => 'Visualization', 'description' => 'Communicating insights visually', 'duration_minutes' => 21, 'content_url' => 'https://example.com/data/viz'],
            ['title' => 'Statistical Inference', 'description' => 'Hypothesis testing basics', 'duration_minutes' => 29, 'content_url' => 'https://example.com/data/inference'],
        ]);
    }

    private function addLessons($courseId, $lessons)
    {
        // Only add lessons if the course exists and has no lessons (to avoid duplicates on re-seed)
        if (Course::where('id', $courseId)->exists() && Lesson::where('course_id', $courseId)->doesntExist()) {
            $position = 1;
            foreach ($lessons as $lessonData) {
                $lessonData['course_id'] = $courseId;
                $lessonData['position'] = $position++;
                Lesson::create($lessonData);
            }
        }
    }
}
// End of file (clearing old content)
