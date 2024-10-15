<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => $this->faker->numberBetween(1, 10),
            'priority' => $this->faker->numberBetween(1, 10),
            'chapter_title' => $this->faker->text(),
            'status' => $this->faker->randomElement(['disable', 'enable']),
        ];
    }
}
