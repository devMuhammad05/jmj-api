<?php

namespace App\Jobs;

use App\Enums\MetaTraderCredentialConnectionStatus;
use App\Models\MetaTraderCredential;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchAllTradingStatsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        MetaTraderCredential::query()
            ->where('status', MetaTraderCredentialConnectionStatus::Connected)
            ->whereNotNull('account_id')
            ->each(fn (MetaTraderCredential $credential) => FetchTradingStats::dispatch($credential));
    }
}
