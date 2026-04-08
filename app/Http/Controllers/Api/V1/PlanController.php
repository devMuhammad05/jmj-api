<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PlanController extends ApiController
{
    /**
     * List all active plans, optionally filtered by type (signals|trading).
     */
    public function index(Request $request): JsonResponse
    {
        $plans = Plan::where('is_active', true)
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            // ->orderBy('type')
            ->orderBy('level')
            ->get();

        return $this->successResponse('Plans retrieved successfully', PlanResource::collection($plans));
    }
}
