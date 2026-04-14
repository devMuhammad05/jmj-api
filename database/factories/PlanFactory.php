<?php

namespace Database\Factories;

use App\Enums\PlanType;
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
            'name' => fake()->unique()->words(2, true),
            'price' => fake()->randomFloat(2, 10, 500),
            'duration_days' => fake()->randomElement([30, 60, 90, 180, 365]),
            'is_active' => true,
            'level' => 1,
            'type' => PlanType::TradingClasses,
        ];
    }
}
