<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CommentLike::factory(50)->create();
    }
}
