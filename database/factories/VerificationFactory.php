<?php

namespace Database\Factories;

use App\Enums\IdType;
use App\Enums\VerificationStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Verification>
 */
class VerificationFactory extends Factory
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
            'id_type' => fake()->randomElement(IdType::cases()),
            'id_number' => fake()->numerify('ID-##########'),
            'id_card_front_url' => 'https://example.com/verifications/'.fake()->uuid().'.jpg',
            'id_card_back_url' => 'https://example.com/verifications/'.fake()->uuid().'.jpg',
            'selfie_url' => 'https://example.com/verifications/'.fake()->uuid().'.jpg',
            'status' => fake()->randomElement(VerificationStatus::cases()),
        ];
    }
}
