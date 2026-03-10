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
            "title" => $this->faker->sentence(4),
            "description" => $this->faker->paragraph(),
            "scheduled_at" => $this->faker->dateTimeBetween("now", "+1 month"),
            "platform" => $this->faker->randomElement(ClassPlatform::cases()),
            "meeting_link" => $this->faker->url(),
            "is_published" => $this->faker->boolean(80),
        ];
    }
}
