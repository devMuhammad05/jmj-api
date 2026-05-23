<?php

namespace Tests\Feature;

use App\Enums\PayoutAccountType;
use App\Models\PayoutAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/payout-accounts')->assertStatus(401);
        $this->postJson('/api/v1/payout-accounts', [])->assertStatus(401);
    }

    public function test_can_list_payout_accounts(): void
    {
        PayoutAccount::factory()->bank()->create(['user_id' => $this->user->id]);
        PayoutAccount::factory()->crypto()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/payout-accounts');

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => ['id', 'type', 'label', 'is_default', 'created_at'],
            ],
        ])->assertJsonCount(2, 'data');
    }

    public function test_user_only_sees_own_payout_accounts(): void
    {
        $otherUser = User::factory()->create();
        PayoutAccount::factory()->bank()->create(['user_id' => $otherUser->id]);
        PayoutAccount::factory()->bank()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/payout-accounts');

        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_can_add_bank_payout_account(): void
    {
        $payload = [
            'type' => 'bank',
            'label' => 'My GTB Account',
            'bank_name' => 'GTBank',
            'account_name' => 'John Doe',
            'account_number' => '0123456789',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', $payload);

        $response->assertStatus(201)->assertJson([
            'status' => 'success',
            'data' => [
                'type' => PayoutAccountType::Bank->value,
                'label' => 'My GTB Account',
                'bank_name' => 'GTBank',
                'account_name' => 'John Doe',
                'account_number' => '0123456789',
                'is_default' => false,
            ],
        ]);

        $this->assertDatabaseHas('payout_accounts', [
            'user_id' => $this->user->id,
            'type' => 'bank',
            'bank_name' => 'GTBank',
        ]);
    }

    public function test_can_add_crypto_payout_account(): void
    {
        $payload = [
            'type' => 'crypto',
            'label' => 'My USDT Wallet',
            'wallet_address' => 'TRx123456789abcdefghij',
            'network' => 'TRC20',
            'coin' => 'USDT',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', $payload);

        $response->assertStatus(201)->assertJson([
            'status' => 'success',
            'data' => [
                'type' => PayoutAccountType::Crypto->value,
                'wallet_address' => 'TRx123456789abcdefghij',
                'network' => 'TRC20',
                'coin' => 'USDT',
            ],
        ]);

        $this->assertDatabaseHas('payout_accounts', [
            'user_id' => $this->user->id,
            'type' => 'crypto',
            'coin' => 'USDT',
        ]);
    }

    public function test_bank_account_requires_bank_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', [
            'type' => 'bank',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'bank_name',
            'account_name',
            'account_number',
        ]);
    }

    public function test_crypto_account_requires_crypto_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', [
            'type' => 'crypto',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'wallet_address',
            'network',
            'coin',
        ]);
    }

    public function test_invalid_type_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', [
            'type' => 'paypal',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['type']);
    }

    public function test_setting_default_clears_previous_default(): void
    {
        $existing = PayoutAccount::factory()->bank()->isDefault()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->postJson('/api/v1/payout-accounts', [
            'type' => 'bank',
            'bank_name' => 'Access Bank',
            'account_name' => 'John Doe',
            'account_number' => '9876543210',
            'is_default' => true,
        ]);

        $this->assertDatabaseHas('payout_accounts', [
            'id' => $existing->id,
            'is_default' => false,
        ]);

        $this->assertDatabaseHas('payout_accounts', [
            'account_number' => '9876543210',
            'is_default' => true,
        ]);
    }

    public function test_bank_fields_not_shown_for_crypto_account(): void
    {
        PayoutAccount::factory()->crypto()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/payout-accounts');

        $account = $response->json('data.0');

        $this->assertArrayNotHasKey('bank_name', $account);
        $this->assertArrayNotHasKey('account_name', $account);
        $this->assertArrayNotHasKey('account_number', $account);
    }

    public function test_crypto_fields_not_shown_for_bank_account(): void
    {
        PayoutAccount::factory()->bank()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/payout-accounts');

        $account = $response->json('data.0');

        $this->assertArrayNotHasKey('wallet_address', $account);
        $this->assertArrayNotHasKey('network', $account);
        $this->assertArrayNotHasKey('coin', $account);
    }
}
