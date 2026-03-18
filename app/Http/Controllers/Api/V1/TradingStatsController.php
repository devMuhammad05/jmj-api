<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\MetaTraderCredential;
use App\Services\TradingStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingStatsController extends ApiController
{
    public function __construct(public TradingStatsService $tradingStats) {}

    /**
     * Return trading stats for the authenticated user.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var MetaTraderCredential|null $credential */
        $credential = MetaTraderCredential::query()
            // ->where('user_id', $request->user()->id)
            ->whereNotNull('account_id')
            ->latest()
            ->first();

        if (! $credential) {
            return $this->successResponse('No MetaTrader account found');
        }

        $metric = $this->tradingStats->getStats($credential);

        if (! $metric) {
            return $this->successResponse('No trading stats available yet');
        }

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
