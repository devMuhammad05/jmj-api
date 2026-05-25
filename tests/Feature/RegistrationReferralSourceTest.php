<?php

namespace Tests\Feature;

use App\Enums\ReferralSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationReferralSourceTest extends TestCase
{
    use RefreshDatabase;

    private array $basePayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePayload = [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
    }

    public function test_referral_sources_endpoint_is_public(): void
    {
        $this->getJson('/api/v1/referral-sources')->assertStatus(200);
    }

    public function test_referral_sources_returns_all_sources(): void
    {
        $response = $this->getJson('/api/v1/referral-sources');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => ['value', 'label'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertCount(count(ReferralSource::cases()), $data);
    }

    public function test_can_register_with_referral_source(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/auth/register', array_merge($this->basePayload, [
            'referral_source' => 'instagram',
        ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'referral_source' => 'instagram',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertEquals(ReferralSource::Instagram, $user->referral_source);
    }

    public function test_can_register_without_referral_source(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/v1/auth/register', $this->basePayload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'referral_source' => null,
        ]);
    }

    public function test_rejects_invalid_referral_source(): void
    {
        $response = $this->postJson('/api/v1/auth/register', array_merge($this->basePayload, [
            'referral_source' => 'invalid_source',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['referral_source']);
    }

    public function test_all_valid_referral_sources_are_accepted(): void
    {
        Notification::fake();

        foreach (ReferralSource::cases() as $index => $source) {
            $payload = array_merge($this->basePayload, [
                'email' => "user{$index}@example.com",
                'referral_source' => $source->value,
            ]);

            $this->postJson('/api/v1/auth/register', $payload)
                ->assertStatus(201);

            $this->assertDatabaseHas('users', [
                'email' => "user{$index}@example.com",
                'referral_source' => $source->value,
            ]);
        }
    }
}
