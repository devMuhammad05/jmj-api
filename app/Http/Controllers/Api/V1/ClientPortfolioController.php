<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\MetaAccountMetric;
use App\Models\MetaTraderCredential;
use Illuminate\Http\JsonResponse;

class ClientPortfolioController extends ApiController
{
public function index(): JsonResponse
    {
        $credentials = MetaTraderCredential::query()
            ->with('user')
            ->whereNotNull('account_id')
            ->get();

        $metrics = MetaAccountMetric::query()
            ->whereIn('account_id', $credentials->pluck('account_id'))
            ->get()
            ->keyBy('account_id');

        $portfolio = $credentials
            ->filter(fn (MetaTraderCredential $credential) => $metrics->has($credential->account_id))
            ->map(fn (MetaTraderCredential $credential) => $this->formatEntry($credential, $metrics->get($credential->account_id)))
            ->sortByDesc('balance')
            ->take(3)
            ->values();

        return $this->successResponse('Client portfolio retrieved successfully', $portfolio);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatEntry(MetaTraderCredential $credential, MetaAccountMetric $metric): array
    {
        $profitPercent = $metric->deposits > 0
            ? round(($metric->profit / $metric->deposits) * 100, 2)
            : 0.0;

        return [
            'name' => $credential->user->full_name,
            'risk_level' => $credential->risk_level->value,
            'balance' => $metric->balance,
            'profit' => $metric->profit,
            'profit_percent' => $profitPercent,
        ];
    }


}
