<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => $this->faker->numberBetween(1, 10),
            'category_name' => $this->faker->unique()->word(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement([1,0]),
        ];
    }
}
