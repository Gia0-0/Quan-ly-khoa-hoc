<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => $this->faker->numberBetween(1, 10),
            'user_id' => 1,
            'course_name' => $this->faker->unique()->word(),
            'course_description' => $this->faker->text(),
            'slug' => $this->faker->slug(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'price_sale' => $this->faker->randomFloat(2, 9, 90),
            'image_path' => $this->faker->imageUrl(100, 100),
            'image_name' => $this->faker->unique()->word(),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'expert', 'all']),
        ];
    }
}
