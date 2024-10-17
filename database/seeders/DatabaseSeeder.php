<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
           UserSeeder::class,
           CourseSeeder::class,
           CourseInfoSeeder::class,
           ChapterSeeder::class,
           LessonSeeder::class,
           CategorySeeder::class,
           UserCourseSeeder::class,
           UserDetailSeeder::class,
           CommentSeeder::class,
           CommentLikeSeeder::class,
           DiscountCategoriesSeeder::class,
           DiscountSeeder::class,
           NotificationSeeder::class,
           OrderItemSeeder::class,
           OrderSeeder::class,
           PaymentSeeder::class,
           ReviewSeeder::class,
           TagCourseSeeder::class,
           TagSeeder::class,
           WishlistSeeder::class
        ]);
    }
}
