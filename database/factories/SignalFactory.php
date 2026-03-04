<?php

namespace Database\Factories;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Signal>
 */
class SignalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entry = fake()->randomFloat(5, 1, 100);

        return [
            'symbol' => fake()->randomElement(['EURUSD', 'GBPUSD', 'USDJPY', 'XAUUSD', 'BTCUSD', 'ETHUSD']),
            'action' => fake()->randomElement(SignalAction::cases()),
            'type' => fake()->randomElement(SignalType::cases()),
            'entry_price' => $entry,
            'stop_loss' => $entry * 0.95,
            'take_profit_1' => $entry * 1.05,
            'take_profit_2' => $entry * 1.10,
            'take_profit_3' => $entry * 1.15,
            'status' => fake()->randomElement(SignalStatus::cases()),
            'pips_result' => fake()->randomFloat(2, -100, 300),
            'notes' => fake()->sentence(),
            'is_published' => fake()->boolean(80),
        ];
    }
}
