<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\User\SubscriptionExpiringNotification;
use Illuminate\Console\Command;

class SendSubscriptionExpiringReminders extends Command
{
    protected $signature = 'notifications:subscription-expiring';

    protected $description = 'Notify users whose active subscriptions expire in 3 days.';

    public function handle(): int
    {
        $subscriptions = Subscription::query()
            ->with(['user', 'plan'])
            ->where('is_active', true)
            ->whereBetween('ends_at', [now()->startOfDay()->addDays(3), now()->endOfDay()->addDays(3)])
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->user->notify(new SubscriptionExpiringNotification($subscription));
        }

        $this->info("Sent {$subscriptions->count()} expiring subscription reminder(s).");

        return self::SUCCESS;
    }
}
