<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\RateResource;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;

class RateController extends ApiController
{
    /**
     * List all rates.
     */
    public function index(): JsonResponse
    {
        $rates = Rate::all();

        return $this->successResponse('Rates retrieved successfully', RateResource::collection($rates));
    }

    /**
     * Get a specific rate by key.
     */
    public function show(string $key): JsonResponse
    {
        $rate = Rate::where('key', $key)->first();

        if (! $rate) {
            return $this->errorResponse('Rate not found', 404);
        }

        return $this->successResponse('Rate retrieved successfully', new RateResource($rate));
    }
}
