<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PlanType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TradingClassResource;
use App\Models\TradingClass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingClassController extends Controller
{
    /**
     * Display a listing of the published trading classes.
     */
    public function index(Request $request): JsonResponse
    {
        $subscription = $request->user()?->activeSubscriptionFor(PlanType::TradingClasses);

        $classes = TradingClass::query()
            ->where('is_published', true)
            ->where(function (Builder $q) use ($subscription): void {
                $q->where('is_free', true);

                if ($subscription) {
                    $q->orWhereHas(
                        'plans',
                        fn (Builder $pq) => $pq->where('plans.id', $subscription->plan_id),
                    );
                }
            })
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Trading classes retrieved successfully',
            'data' => TradingClassResource::collection($classes),
        ]);
    }

    /**
     * Display the specified trading class.
     */
    public function show(Request $request, TradingClass $tradingClass): JsonResponse
    {
        if (! $tradingClass->is_published) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Class not found',
                ],
                404,
            );
        }

        $subscription = $request->user()?->activeSubscriptionFor(PlanType::TradingClasses);
        $accessible = $tradingClass->is_free
            || ($subscription && $tradingClass->plans()->where('plans.id', $subscription->plan_id)->exists());

        if (! $accessible) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'An active subscription is required to access this resource.',
                ],
                403,
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Trading class details retrieved successfully',
            'data' => new TradingClassResource($tradingClass),
        ]);
    }
}
