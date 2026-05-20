<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PoolInvestmentStatus;
use App\Enums\PoolStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\PoolResource;
use App\Models\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoolController extends ApiController
{
    /**
     * Display a listing of active pools.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $pools = Pool::where('status', PoolStatus::ACTIVE)
            ->withCount(['poolInvestments as approved_investors_count' => fn ($q) => $q->where('status', PoolInvestmentStatus::VERIFIED)])
            ->with(['poolInvestments' => fn ($q) => $q->where('user_id', $userId)])
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            'Active pools retrieved successfully',
            PoolResource::collection($pools)
        );
    }

    /**
     * Display the specified pool.
     */
    public function show(Request $request, Pool $pool): JsonResponse
    {
        $userId = $request->user()->id;

        $pool->loadCount(['poolInvestments as approved_investors_count' => fn ($q) => $q->where('status', PoolInvestmentStatus::VERIFIED)])
            ->load(['poolInvestments' => fn ($q) => $q->where('user_id', $userId)]);

        return $this->successResponse(
            'Pool details retrieved successfully',
            new PoolResource($pool)
        );
    }
}
