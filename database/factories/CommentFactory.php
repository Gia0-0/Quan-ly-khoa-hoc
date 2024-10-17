<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 10),
            'course_id' => $this->faker->numberBetween(1, 10),
            'parent_id' => $this->faker->numberBetween(1, 10),
            'content' => $this->faker->text(),
            'depth' => $this->faker->randomElement([1,2,3]),
            'image_path' => $this->faker->imageUrl(100, 100),
            'image_name' => $this->faker->word(),
        ];
    }
}
