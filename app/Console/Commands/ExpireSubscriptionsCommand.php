<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark expired subscriptions as inactive';

    public function handle(): int
    {
        $count = Subscription::where('is_active', true)
            ->where('ends_at', '<', now())
            ->update(['is_active' => false]);

        $this->info("Marked {$count} subscription(s) as inactive.");

        return self::SUCCESS;
    }
}
