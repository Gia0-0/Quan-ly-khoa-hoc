<?php

namespace Database\Factories;

use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentLike>
 */
class CommentLikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = $this->faker->numberBetween(1, 10); // Adjust the range as needed
        $commentId = $this->faker->numberBetween(1, 10); // Adjust the range as needed

        // Ensure the combination of user_id and comment_id is unique
        while (CommentLike::where('user_id', $userId)->where('comment_id', $commentId)->exists()) {
            $userId = $this->faker->numberBetween(1, 10); // Generate a new user_id
            $commentId = $this->faker->numberBetween(1, 10); // Generate a new comment_id
        }

        return [
            'user_id' => $userId,
            'comment_id' => $commentId,
        ];
    }
}
