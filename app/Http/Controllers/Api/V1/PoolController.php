<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

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
        $pools = Pool::where('status', PoolStatus::ACTIVE)
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
    public function show(Pool $pool): JsonResponse
    {
        return $this->successResponse(
            'Pool details retrieved successfully',
            new PoolResource($pool)
        );
    }
}
