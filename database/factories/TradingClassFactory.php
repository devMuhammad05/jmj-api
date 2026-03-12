<?php

namespace Database\Factories;

use App\Enums\ClassPlatform;
use App\Models\TradingClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TradingClass>
 */
class TradingClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TradingClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'scheduled_at' => fake()->dateTimeBetween('now', '+1 month'),
            'platform' => fake()->randomElement(ClassPlatform::cases()),
            'meeting_link' => fake()->url(),
            'is_published' => fake()->boolean(80),
        ];
    }
}
