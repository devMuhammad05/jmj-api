<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MetaTraderCredential>
 */
class MetaTraderCredentialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'mt_account_number' => fake()->numerify('#######'),
            'mt_password' => fake()->password(8, 12),
            'mt_server' => 'Broker-Real'.fake()->numberBetween(1, 10),
            'initial_deposit' => fake()->randomFloat(2, 500, 50000),
            'risk_level' => fake()->randomElement(['Low', 'Medium', 'High']),
        ];
    }
}
