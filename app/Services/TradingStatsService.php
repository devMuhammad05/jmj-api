<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MetaAccountMetric;
use App\Models\MetaTraderCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TradingStatsService
{
    /**
     * Return trading stats for a credential, refreshing from the fast backend
     * if the cached metrics are older than 10 minutes.
     */
    public function getStats(MetaTraderCredential $credential): ?MetaAccountMetric
    {
        /** @var MetaAccountMetric|null $metric */
        $metric = MetaAccountMetric::query()
            ->where('account_id', $credential->account_id)
            ->latest()
            ->first();

        if ($metric && $metric->updated_at->diffInMinutes(now()) < 10) {
            return $metric;
        }

        $this->syncMetrics($credential);

        return MetaAccountMetric::query()
            ->where('account_id', $credential->account_id)
            ->latest()
            ->first();
    }

    /**
     * Request the fast backend to sync the latest stats for the given account.
     * The fast backend is responsible for persisting the updated metrics.
     */
    private function syncMetrics(MetaTraderCredential $credential): void
    {
        $response = Http::baseUrl(config('services.fast_backend.base_url'))
            ->post('/trading-stats', [
                'account_id' => $credential->account_id,
            ]);

        Log::info('TradingStats refresh response', [
            'account_id' => $credential->account_id,
            'status' => $response->status(),
            'body' => $response->json(),
        ]);
    }
}
