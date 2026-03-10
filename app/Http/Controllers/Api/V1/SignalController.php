<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GetSignalsAction;
use App\Enums\SignalStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\SignalResource;
use App\Models\Signal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SignalController extends ApiController
{
    /**
     * Display a listing of published signals.
     */
    public function index(
        Request $request,
        GetSignalsAction $action,
    ): AnonymousResourceCollection {
        $signals = $action->execute($request);

        return SignalResource::collection($signals);
    }

    /**
     * Display the specified signal.
     */
    public function show(Signal $signal): JsonResponse
    {
        // Only show published signals
        if (! $signal->is_published) {
            return $this->errorResponse('Signal not found', 404);
        }

        return $this->successResponse(
            'Signal retrieved successfully',
            new SignalResource($signal),
        );
    }

    /**
     * Get active signals only.
     */
    public function active(
        Request $request,
        GetSignalsAction $action,
    ): AnonymousResourceCollection {
        $signals = $action->execute($request, activeOnly: true);

        return SignalResource::collection($signals);
    }

    /**
     * Get signal statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_signals' => Signal::where('is_published', true)->count(),
            'active_signals' => Signal::where('is_published', true)
                ->where('status', SignalStatus::ACTIVE)
                ->count(),
            'tp_signals' => Signal::where('is_published', true)
                ->where('status', SignalStatus::TP)
                ->count(),
            'sl_signals' => Signal::where('is_published', true)
                ->where('status', SignalStatus::SL)
                ->count(),
            'total_pips' => Signal::where('is_published', true)
                ->whereNotNull('pips_result')
                ->sum('pips_result'),
            'average_pips' => Signal::where('is_published', true)
                ->whereNotNull('pips_result')
                ->avg('pips_result'),
            'win_rate' => $this->calculateWinRate(),
        ];

        return $this->successResponse(
            'Signal statistics retrieved successfully',
            $stats,
        );
    }

    /**
     * Calculate win rate percentage.
     */
    private function calculateWinRate(): float
    {
        $totalClosed = Signal::where('is_published', true)
            ->whereIn('status', [
                SignalStatus::TP,
                SignalStatus::SL,
                SignalStatus::CLOSED,
            ])
            ->count();

        if ($totalClosed === 0) {
            return 0.0;
        }

        $wins = Signal::where('is_published', true)
            ->where('status', SignalStatus::TP)
            ->count();

        return round(($wins / $totalClosed) * 100, 2);
    }
}
