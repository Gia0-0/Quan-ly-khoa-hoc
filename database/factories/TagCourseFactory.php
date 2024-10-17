<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagCourse>
 */
class TagCourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => $this->faker->unique()->numberBetween(1, 10),
            'course_id' => $this->faker->unique()->numberBetween(1, 10),
        ];
    }
}
