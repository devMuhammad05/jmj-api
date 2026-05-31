<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Listeners\SendAdminLoginNotification;
use App\Models\User;
use App\Notifications\Admin\AdminLoginNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminLoginNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_sends_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => Role::Admin]);

        $listener = new SendAdminLoginNotification;
        $listener->handle(new Login('web', $admin, false));

        Notification::assertSentTo($admin, AdminLoginNotification::class);
    }

    public function test_regular_user_login_does_not_send_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role' => Role::User]);

        $listener = new SendAdminLoginNotification;
        $listener->handle(new Login('web', $user, false));

        Notification::assertNotSentTo($user, AdminLoginNotification::class);
    }

    public function test_notification_contains_expected_data(): void
    {
        $notification = new AdminLoginNotification(
            ipAddress: '192.168.1.1',
            userAgent: 'Mozilla/5.0',
            loginAt: '2026-05-31 10:00:00',
        );

        $payload = $notification->toArray(new User);

        $this->assertSame('admin_login', $payload['type']);
        $this->assertSame('192.168.1.1', $payload['ip_address']);
        $this->assertSame('Mozilla/5.0', $payload['user_agent']);
        $this->assertSame('2026-05-31 10:00:00', $payload['login_at']);
    }

    public function test_notification_via_channels(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $notification = new AdminLoginNotification(
            ipAddress: '10.0.0.1',
            userAgent: 'Chrome',
            loginAt: now()->toDateTimeString(),
        );

        $this->assertSame(['mail', 'database', 'broadcast'], $notification->via($admin));
    }
}
