<?php

namespace Tests\Feature;

use App\Actions\ApprovePaymentAction;
use App\Enums\PaymentStatus;
use App\Mail\SubscriptionApprovedMail;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ApprovePaymentActionTest extends TestCase
{
    use RefreshDatabase;

    private function createPayment(?array $planOverrides = []): Payment
    {
        $user = User::factory()->create();

        $plan = Plan::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'price' => 199.00,
            'duration_days' => 30,
            'is_active' => true,
        ] + $planOverrides);

        return Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => PaymentStatus::Submitted,
            'reference' => 'REF-001',
        ]);
    }

    public function test_it_approves_payment_and_creates_active_subscription(): void
    {
        Mail::fake();

        $payment = $this->createPayment();

        $subscription = (new ApprovePaymentAction)->execute($payment);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertTrue($subscription->is_active);
        $this->assertEquals($payment->plan_id, $subscription->plan_id);
        $this->assertEquals($payment->user_id, $subscription->user_id);
        $this->assertEquals(PaymentStatus::Approved, $payment->fresh()->status);
    }

    public function test_it_deactivates_previous_subscriptions_on_approval(): void
    {
        Mail::fake();

        $payment = $this->createPayment();

        $existingSubscription = Subscription::create([
            'user_id' => $payment->user_id,
            'plan_id' => $payment->plan_id,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
            'is_active' => true,
        ]);

        (new ApprovePaymentAction)->execute($payment);

        $this->assertFalse($existingSubscription->fresh()->is_active);
    }

    public function test_it_sends_subscription_approved_email_to_user(): void
    {
        Mail::fake();

        $payment = $this->createPayment();

        (new ApprovePaymentAction)->execute($payment);

        Mail::assertSent(SubscriptionApprovedMail::class, function (SubscriptionApprovedMail $mail) use ($payment) {
            return $mail->hasTo($payment->user->email);
        });
    }

    public function test_email_contains_correct_subscription_data(): void
    {
        Mail::fake();

        $payment = $this->createPayment();

        $subscription = (new ApprovePaymentAction)->execute($payment);

        Mail::assertSent(SubscriptionApprovedMail::class, function (SubscriptionApprovedMail $mail) use ($subscription) {
            return $mail->subscription->is($subscription);
        });
    }
}
