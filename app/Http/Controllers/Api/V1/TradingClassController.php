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
        $userPlan = $subscription?->plan;

        $classes = TradingClass::query()
            ->where('is_published', true)
            ->where(function (Builder $q) use ($userPlan): void {
                $q->where('is_free', true);

                if ($userPlan) {
                    $q->orWhereHas(
                        'plans',
                        fn (Builder $pq) => $pq->where('plans.type', $userPlan->type)
                            ->where('plans.level', '<=', $userPlan->level),
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
        $userPlan = $subscription?->plan;
        $accessible = $tradingClass->is_free
            || ($userPlan && $tradingClass->plans()
                ->where('plans.type', $userPlan->type)
                ->where('plans.level', '<=', $userPlan->level)
                ->exists());

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
