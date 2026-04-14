<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\User\SubscriptionActivatedNotification;
use Illuminate\Support\Facades\DB;

class ApprovePaymentAction
{
    public function execute(Payment $payment): Subscription
    {
        return DB::transaction(function () use ($payment) {
            $payment->update(['status' => PaymentStatus::Approved]);

            $payment->user->subscriptions()
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $payment->loadMissing('plan');

            $subscription = $payment->user->subscriptions()->create([
                'plan_id' => $payment->plan_id,
                'payment_id' => $payment->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($payment->plan->duration_days),
                'is_active' => true,
            ]);

            $subscription->load(['user', 'plan']);

            $payment->user->notify(new SubscriptionActivatedNotification($subscription));

            return $subscription;
        });
    }
}
