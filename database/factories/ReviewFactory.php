<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'parent_review_id'=> $this->faker->integer(),
            'course_id' => $this->faker->unique()->numberBetween(1, 10),
            'course_name' => $this->faker->unique()->word(),
            'content' => $this->faker->text(),
            'rating' => $this->faker->randomElement([1,1.5,2,2.5,3,3.5,4,4.5]),
        ];
    }
}
