<?php

namespace Database\Factories;

use App\Enums\PayoutAccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayoutAccount>
 */
class PayoutAccountFactory extends Factory
{
    public function definition(): array
    {
        return $this->bank()->definition();
    }

    public function bank(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory(),
            'type' => PayoutAccountType::Bank,
            'label' => fake()->optional()->words(2, true),
            'is_default' => false,
            'bank_name' => fake()->company(),
            'account_name' => fake()->name(),
            'account_number' => fake()->numerify('##########'),
            'wallet_address' => null,
            'network' => null,
            'coin' => null,
        ]);
    }

    public function crypto(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory(),
            'type' => PayoutAccountType::Crypto,
            'label' => fake()->optional()->words(2, true),
            'is_default' => false,
            'bank_name' => null,
            'account_name' => null,
            'account_number' => null,
            'wallet_address' => fake()->regexify('[A-Za-z0-9]{34}'),
            'network' => fake()->randomElement(['TRC20', 'ERC20', 'BEP20', 'BTC']),
            'coin' => fake()->randomElement(['USDT', 'BTC', 'ETH', 'BNB']),
        ]);
    }

    public function isDefault(): static
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}
