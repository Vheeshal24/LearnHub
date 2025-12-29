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
                'title' => 'Laravel Basics', 
                'slug' => 'laravel-basics', 
                'category' => 'Programming', 
                'tags' => 'laravel,php', 
                'image_url' => NULL, 
                'description' => 'Learn Laravel fundamentals', 
                'published_at' => '2025-12-24 15:38:06', 
                'views_count' => 0, 
                'enrollments_count' => 0, 
                'rating' => 5, 
                'created_at' => '2025-12-24 15:38:06', 
                'updated_at' => '2025-12-24 15:38:06'
            ],
            [
                'id' => 2, 
                'title' => 'PHP Fundamentals', 
                'slug' => 'php-fundamentals', 
                'category' => 'Programming', 
                'tags' => 'php,backend', 
                'image_url' => NULL, 
                'description' => 'PHP core concepts', 
                'published_at' => '2025-12-24 15:38:06', 
                'views_count' => 0, 
                'enrollments_count' => 1, 
                'rating' => 4, 
                'created_at' => '2025-12-24 15:38:06', 
                'updated_at' => '2025-12-26 21:33:49'
            ],
        ];

        // Using upsert to handle potential duplicates on ID
        DB::table('courses')->upsert($courses, ['id']);

        // 2. Insert Lessons
        $lessons = [
            [
                'id' => 1, 'course_id' => 1, 'title' => 'Intro to Laravel', 'slug' => 'intro-laravel',
                'description' => NULL, 'content_url' => NULL, 'material_file' => NULL, 'quiz_json' => NULL,
                'duration_minutes' => 15, 'position' => 1, 'published' => 1,
                'created_at' => '2025-12-24 15:38:32', 'updated_at' => '2025-12-24 15:38:32'
            ],
            [
                'id' => 2, 'course_id' => 1, 'title' => 'Laravel Routing', 'slug' => 'laravel-routing',
                'description' => NULL, 'content_url' => NULL, 'material_file' => NULL, 'quiz_json' => NULL,
                'duration_minutes' => 20, 'position' => 2, 'published' => 1,
                'created_at' => '2025-12-24 15:38:32', 'updated_at' => '2025-12-24 15:38:32'
            ],
            [
                'id' => 3, 'course_id' => 1, 'title' => 'Laravel Controllers', 'slug' => 'laravel-controllers',
                'description' => NULL, 'content_url' => NULL, 'material_file' => NULL, 'quiz_json' => NULL,
                'duration_minutes' => 25, 'position' => 3, 'published' => 1,
                'created_at' => '2025-12-24 15:38:32', 'updated_at' => '2025-12-24 15:38:32'
            ],
            [
                'id' => 4, 'course_id' => 2, 'title' => 'PHP Syntax', 'slug' => 'php-syntax',
                'description' => NULL, 'content_url' => NULL, 'material_file' => NULL, 'quiz_json' => NULL,
                'duration_minutes' => 15, 'position' => 1, 'published' => 1,
                'created_at' => '2025-12-24 15:38:32', 'updated_at' => '2025-12-24 15:38:32'
            ],
            [
                'id' => 5, 'course_id' => 2, 'title' => 'PHP Functions', 'slug' => 'php-functions',
                'description' => NULL, 'content_url' => NULL, 'material_file' => NULL, 'quiz_json' => NULL,
                'duration_minutes' => 20, 'position' => 2, 'published' => 1,
                'created_at' => '2025-12-24 15:38:32', 'updated_at' => '2025-12-24 15:38:32'
            ],
        ];

        DB::table('lessons')->upsert($lessons, ['id']);

        // 3. Insert Quiz Attempts
        $quizAttempts = [
            [
                'id' => 7, 'user_id' => 1, 'lesson_id' => 1, 'score' => 8.00, 'total' => 10.00,
                'created_at' => '2025-12-24 15:39:12', 'updated_at' => '2025-12-24 15:39:12'
            ],
            [
                'id' => 8, 'user_id' => 1, 'lesson_id' => 2, 'score' => 6.00, 'total' => 10.00,
                'created_at' => '2025-12-24 15:39:12', 'updated_at' => '2025-12-24 15:39:12'
            ],
            [
                'id' => 9, 'user_id' => 1, 'lesson_id' => 3, 'score' => 10.00, 'total' => 10.00,
                'created_at' => '2025-12-24 15:39:12', 'updated_at' => '2025-12-24 15:39:12'
            ],
            [
                'id' => 10, 'user_id' => 1, 'lesson_id' => 4, 'score' => 7.00, 'total' => 10.00,
                'created_at' => '2025-12-24 15:39:23', 'updated_at' => '2025-12-24 15:39:23'
            ],
            [
                'id' => 11, 'user_id' => 1, 'lesson_id' => 5, 'score' => 9.00, 'total' => 10.00,
                'created_at' => '2025-12-24 15:39:23', 'updated_at' => '2025-12-24 15:39:23'
            ],
        ];

        DB::table('lesson_quiz_attempts')->upsert($quizAttempts, ['id']);

        // 4. Insert Learning Goals
        $learningGoals = [
            [
                'id' => 1, 'user_id' => 1, 'course_id' => 3, 
                'goal_description' => 'Complete this course', 
                'target_lessons' => 0, 'completed_lessons' => 0, 
                'start_date' => '2025-12-20', 'end_date' => NULL, 
                'target_completion_time' => NULL, 
                'created_at' => '2025-12-20 06:24:08', 'updated_at' => '2025-12-20 06:24:08'
            ],
            [
                'id' => 21, 'user_id' => 1, 'course_id' => 1, 
                'goal_description' => 'test3', 
                'target_lessons' => 0, 'completed_lessons' => 0, 
                'start_date' => '2025-12-27', 'end_date' => NULL, 
                'target_completion_time' => '2025-12-31 23:59:59', 
                'created_at' => '2025-12-27 08:10:01', 'updated_at' => '2025-12-27 08:25:42'
            ],
        ];

        DB::table('learning_goals')->upsert($learningGoals, ['id']);
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
