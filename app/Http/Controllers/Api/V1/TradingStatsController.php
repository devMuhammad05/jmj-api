<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\FetchTradingStats;
use App\Models\MetaAccountMetric;
use App\Models\MetaTraderCredential;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TradingStatsController extends ApiController
{
    /**
     * Return trading stats for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        Log::info('TradingStats: fetching credential', ['user_id' => $request->user()->id]);

        /** @var MetaTraderCredential|null $credential */
        $credential = MetaTraderCredential::query()
            ->where('user_id', $request->user()->id)
            ->whereNotNull('account_id')
            ->latest()
            ->first();

        if (! $credential) {
            Log::warning('TradingStats: no MetaTrader credential found', ['user_id' => $request->user()->id]);

            return $this->successResponse('No MetaTrader account found');
        }

        Log::info('TradingStats: dispatching FetchTradingStats job', [
            'user_id' => $request->user()->id,
            'account_id' => $credential->account_id,
        ]);

        FetchTradingStats::dispatch($credential);

        Log::info('TradingStats: reading cached metric', ['account_id' => $credential->account_id]);

        /** @var MetaAccountMetric|null $metric */
        $metric = MetaAccountMetric::query()
            ->where('account_id', $credential->account_id)
            ->latest()
            ->first();

        if (! $metric) {
            Log::warning('TradingStats: no cached metric available', ['account_id' => $credential->account_id]);

            return $this->successResponse('No trading stats available yet');
        }

        Log::info('TradingStats: returning stats', ['account_id' => $credential->account_id]);

        return $this->successResponse('Trading stats retrieved successfully', [
            'balance' => $metric->balance,
            'equity' => $metric->equity,
            'profit' => $metric->profit,
            'deposits' => $metric->deposits,
            'withdrawals' => $metric->withdrawals,
            'margin' => $metric->margin,
            'free_margin' => $metric->free_margin,
            'trades' => $metric->trades,
            'profit_factor' => $metric->profit_factor,
            'sharpe_ratio' => $metric->sharpe_ratio,
            'won_trades_percent' => $metric->won_trades_percent,
            'lost_trades_percent' => $metric->lost_trades_percent,
            'daily_growth' => $metric->daily_growth,
        ]);
    }
}
