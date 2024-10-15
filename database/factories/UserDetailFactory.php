<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => $this->faker->numberBetween(1,10),
            "family_name" => $this->faker->unique()->word(),
            "given_name" => $this->faker->unique()->word(),
            'image_path' => $this->faker->imageUrl(100, 100),
            "image_name" => $this->faker->unique()->word(),
            "phone_number" => $this->faker->phoneNumber(),
            "dob" => $this->faker->date(),
            "gender" => $this->faker->randomElement(['male', 'female']),
            "slug" => $this->faker->text()
        ];
    }
}
