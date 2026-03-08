<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreatePoolInvestmentAction;
use App\DTOs\PoolInvestmentData;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StorePoolInvestmentRequest;
use App\Http\Resources\V1\PoolInvestmentResource;
use App\Models\PoolInvestment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PoolInvestmentController extends ApiController
{
    /**
     * Display a listing of the user's pool investments.
     */
    public function index(Request $request): JsonResponse
    {
        $investments = $request->user()
            ->poolInvestments()
            ->with('pool')
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            'Pool investments retrieved successfully',
            PoolInvestmentResource::collection($investments)
        );
    }

    /**
     * Store a newly created pool investment.
     */
    public function store(
        StorePoolInvestmentRequest $request,
        CreatePoolInvestmentAction $createPoolInvestment
    ): JsonResponse {
        $investment = $createPoolInvestment->execute(
            $request->user(),
            PoolInvestmentData::fromRequest($request)
        );

        $investment->load('pool');

        return $this->createdResponse(
            'Pool investment application submitted successfully. Payment verification will take 24-48 hours.',
            new PoolInvestmentResource($investment)
        );
    }

    /**
     * Display the specified pool investment.
     */
    public function show(Request $request, PoolInvestment $poolInvestment): JsonResponse
    {
        Gate::authorize('view', $poolInvestment);

        $poolInvestment->load('pool');

        return $this->successResponse(
            'Pool investment details retrieved successfully',
            new PoolInvestmentResource($poolInvestment)
        );
    }
}
