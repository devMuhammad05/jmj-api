<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class RejectSubscriptionAction
{
    public function execute(Subscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $subscription->update([
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
            ]);

            if ($subscription->payment_id !== null) {
                $subscription->payment->update(['status' => PaymentStatus::Rejected]);
            }
        });
    }
}
