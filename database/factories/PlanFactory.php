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
            'name' => fake()->randomElement(['Básico', 'Intermediário', 'Premium', 'VIP']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 50, 500),
            'credits' => fake()->numberBetween(4, 20),
            'duration_days' => fake()->randomElement([30, 60, 90]),
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
