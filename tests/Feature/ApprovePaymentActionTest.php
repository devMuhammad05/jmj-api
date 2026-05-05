<?php

namespace Tests\Feature;

use App\Actions\ApprovePaymentAction;
use App\Actions\ApproveSubscriptionAction;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\User\SubscriptionActivatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApprovePaymentActionTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingSubscription(?array $planOverrides = []): Subscription
    {
        $user = User::factory()->create();

        $plan = Plan::factory()->create(array_merge([
            'price' => 199.00,
            'duration_days' => 30,
            'is_active' => true,
        ], $planOverrides));

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'type' => PaymentType::ClassSubscription,
            'status' => PaymentStatus::Pending,
            'reference' => 'REF-001',
        ]);

        return Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => false,
        ]);
    }

    public function test_it_approves_subscription_and_activates_it(): void
    {
        Notification::fake();

        $subscription = $this->createPendingSubscription();

        $result = (new ApproveSubscriptionAction)->execute($subscription);

        $this->assertInstanceOf(Subscription::class, $result);
        $this->assertTrue($result->is_active);
        $this->assertNotNull($result->starts_at);
        $this->assertNotNull($result->ends_at);
    }

    public function test_it_approves_linked_payment_as_side_effect(): void
    {
        Notification::fake();

        $subscription = $this->createPendingSubscription();
        $payment = $subscription->payment;

        (new ApproveSubscriptionAction)->execute($subscription);

        $this->assertEquals(PaymentStatus::Approved, $payment->fresh()->status);
    }

    public function test_it_deactivates_previous_subscriptions_on_approval(): void
    {
        Notification::fake();

        $subscription = $this->createPendingSubscription();

        $existingSubscription = Subscription::create([
            'user_id' => $subscription->user_id,
            'plan_id' => $subscription->plan_id,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'is_active' => true,
        ]);

        (new ApproveSubscriptionAction)->execute($subscription);

        $this->assertFalse($existingSubscription->fresh()->is_active);
    }

    public function test_it_sends_subscription_activated_notification_to_user(): void
    {
        Notification::fake();

        $subscription = $this->createPendingSubscription();

        (new ApproveSubscriptionAction)->execute($subscription);

        Notification::assertSentTo($subscription->user, SubscriptionActivatedNotification::class);
    }

    public function test_notification_contains_correct_subscription_data(): void
    {
        Notification::fake();

        $subscription = $this->createPendingSubscription();

        $result = (new ApproveSubscriptionAction)->execute($subscription);

        Notification::assertSentTo(
            $subscription->user,
            SubscriptionActivatedNotification::class,
            fn (SubscriptionActivatedNotification $notification) => $notification->subscription->is($result),
        );
    }

    public function test_approve_payment_action_throws_for_subscription_payments(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'type' => PaymentType::ClassSubscription,
            'status' => PaymentStatus::Pending,
            'reference' => 'REF-002',
        ]);

        $this->expectException(\LogicException::class);

        (new ApprovePaymentAction)->execute($payment);
    }
}
