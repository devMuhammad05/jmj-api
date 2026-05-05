<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Notifications\User\SubscriptionActivatedNotification;
use Illuminate\Support\Facades\DB;

class ApproveSubscriptionAction
{
    public function execute(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            $subscription->user->subscriptions()
                ->where('id', '!=', $subscription->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $subscription->loadMissing('plan');

            $subscription->update([
                'is_active' => true,
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscription->plan->duration_days),
            ]);

            if ($subscription->payment_id !== null) {
                $subscription->payment->update(['status' => PaymentStatus::Approved]);
            }

            $subscription->load(['user', 'plan']);

            $subscription->user->notify(new SubscriptionActivatedNotification($subscription));

            return $subscription;
        });
    }
}
