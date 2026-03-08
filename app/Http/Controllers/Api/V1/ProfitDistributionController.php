<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\ProfitDistributionResource;
use App\Models\ProfitDistribution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProfitDistributionController extends ApiController
{
    /**
     * Display a listing of the user's profit distributions.
     */
    public function index(Request $request): JsonResponse
    {
        $distributions = ProfitDistribution::whereHas('poolInvestment', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })
            ->latest('distribution_date')
            ->paginate(10);

        return $this->successResponse(
            'Profit distributions retrieved successfully',
            ProfitDistributionResource::collection($distributions)
        );
    }

    /**
     * Display the specified profit distribution.
     */
    public function show(Request $request, ProfitDistribution $profitDistribution): JsonResponse
    {
        Gate::authorize('view', $profitDistribution);

        return $this->successResponse(
            'Profit distribution details retrieved successfully',
            new ProfitDistributionResource($profitDistribution)
        );
    }
}
