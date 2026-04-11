<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Mail\SubscriptionApprovedMail;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

            Mail::to($payment->user->email)->send(new SubscriptionApprovedMail($subscription));

            return $subscription;
        });
    }
}
