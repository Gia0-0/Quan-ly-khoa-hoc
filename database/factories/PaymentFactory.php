<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => $this->faker->unique()->numberBetween(1, 10),
            'partner_code' => $this->faker->numberBetween(1, 10),
            'course_name' => $this->faker->unique()->word(),
            'request_id' => $this->faker->unique()->word(),
            'note' => $this->faker->text(),
            'message' => $this->faker->text(),
            'pay_url' => $this->faker->text(),
            'signature' => $this->faker->text(),
        ];
    }
}
