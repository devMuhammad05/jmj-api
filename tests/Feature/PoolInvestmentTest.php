<?php

namespace Tests\Feature;

use App\Enums\PoolInvestmentStatus;
use App\Enums\PoolStatus;
use App\Models\Pool;
use App\Models\PoolInvestment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoolInvestmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Pool $pool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->pool = Pool::create([
            'name' => 'Test Pool',
            'total_amount' => 45000,
            'investor_count' => 23,
            'last_return' => 15.20,
            'minimum_investment' => 1000,
            'status' => PoolStatus::ACTIVE,
        ]);
    }

    public function test_can_list_active_pools(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/pools');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'total_amount',
                        'investor_count',
                        'last_return',
                        'minimum_investment',
                        'status',
                    ]
                ]
            ]);
    }

    public function test_can_get_pool_details(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/pools/{$this->pool->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->pool->id,
                    'name' => 'Test Pool',
                ]
            ]);
    }

    public function test_can_submit_pool_investment(): void
    {
        $data = [
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'John Doe',
            'contribution' => 1000,
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/pool-investments', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'full_name' => 'John Doe',
                    'contribution' => '1000.00',
                    'status' => PoolInvestmentStatus::PENDING->value,
                ]
            ]);

        $this->assertDatabaseHas('pool_investments', [
            'user_id' => $this->user->id,
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'contribution' => 1000,
        ]);
    }

    public function test_cannot_submit_investment_below_minimum(): void
    {
        $data = [
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'John Doe',
            'contribution' => 500, // Below minimum
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => true,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/pool-investments', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['contribution']);
    }

    public function test_cannot_submit_without_terms_acceptance(): void
    {
        $data = [
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'John Doe',
            'contribution' => 1000,
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => false,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/pool-investments', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['terms_accepted']);
    }

    public function test_can_list_user_investments(): void
    {
        PoolInvestment::create([
            'user_id' => $this->user->id,
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'John Doe',
            'contribution' => 1000,
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => true,
            'status' => PoolInvestmentStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/pool-investments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'pool',
                        'full_name',
                        'contribution',
                        'status',
                    ]
                ]
            ]);
    }

    public function test_user_can_only_view_own_investments(): void
    {
        $otherUser = User::factory()->create();
        
        $investment = PoolInvestment::create([
            'user_id' => $otherUser->id,
            'pool_id' => $this->pool->id,
            'full_name' => 'Other User',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'Other User',
            'contribution' => 1000,
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => true,
            'status' => PoolInvestmentStatus::PENDING,
        ]);

        // User should not be able to view another user's investment
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/pool-investments/{$investment->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_view_own_investment(): void
    {
        $investment = PoolInvestment::create([
            'user_id' => $this->user->id,
            'pool_id' => $this->pool->id,
            'full_name' => 'John Doe',
            'phone_number' => '+234 123 456 7890',
            'bank_name' => 'GTBank',
            'account_number' => '0123456789',
            'account_name' => 'John Doe',
            'contribution' => 1000,
            'payment_proof_path' => 'https://example.com/proof.jpg',
            'terms_accepted' => true,
            'status' => PoolInvestmentStatus::PENDING,
        ]);

        // User should be able to view their own investment
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/pool-investments/{$investment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $investment->id,
                    'full_name' => 'John Doe',
                ]
            ]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/pools');
        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/pool-investments', []);
        $response->assertStatus(401);
    }
}
