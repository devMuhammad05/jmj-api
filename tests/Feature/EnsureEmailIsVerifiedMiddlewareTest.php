<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureEmailIsVerifiedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_cannot_access_protected_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/auth/me');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Your email address is not verified. Please verify your email to continue.',
            ]);
    }

    public function test_verified_user_can_access_protected_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/auth/me');

        $response->assertStatus(200);
    }

    public function test_unverified_user_cannot_access_main_auth_group(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/pools');

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Your email address is not verified. Please verify your email to continue.',
            ]);
    }

    public function test_unverified_user_cannot_access_pin_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/auth/pin/setup', [
            'pin' => '1234',
            'pin_confirmation' => '1234',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Your email address is not verified. Please verify your email to continue.',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    public function test_get_otp_is_accessible_without_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/get-otp?email=test@example.com');

        // Should not be blocked by auth middleware (404 means no OTP found, not blocked)
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'No OTP found for this email.',
            ]);
    }
}
