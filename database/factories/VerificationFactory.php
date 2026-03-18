<?php

namespace Database\Factories;

use App\Enums\IdType;
use App\Enums\VerificationStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'id_type' => $this->faker->randomElement(IdType::cases()),
            'id_number' => $this->faker->numerify('ID-##########'),
            'id_card_front_url' => 'https://example.com/verifications/'.$this->faker->uuid().'.jpg',
            'id_card_back_url' => 'https://example.com/verifications/'.$this->faker->uuid().'.jpg',
            'selfie_url' => 'https://example.com/verifications/'.$this->faker->uuid().'.jpg',
            'status' => $this->faker->randomElement(VerificationStatus::cases()),
        ];
    }
}
