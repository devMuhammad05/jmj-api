<?php

namespace App\Jobs;

use App\Models\MetaTraderCredential;
use App\Services\TradingStatsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchTradingStats implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(public MetaTraderCredential $credential) {}

    public function handle(TradingStatsService $tradingStats): void
    {
        $tradingStats->getStats($this->credential);
    }
}
