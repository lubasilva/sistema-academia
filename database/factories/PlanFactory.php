<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Básico', 'Intermediário', 'Premium', 'VIP']),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'credits' => $this->faker->numberBetween(4, 20),
            'duration_days' => $this->faker->randomElement([30, 60, 90]),
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
