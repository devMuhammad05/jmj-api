<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StorePayoutAccountRequest;
use App\Http\Resources\V1\PayoutAccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayoutAccountController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $accounts = $request->user()->payoutAccounts()->latest()->get();

        return $this->successResponse(
            'Payout accounts retrieved successfully',
            PayoutAccountResource::collection($accounts)
        );
    }

    public function store(StorePayoutAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($request->boolean('is_default')) {
            $user->payoutAccounts()->update(['is_default' => false]);
        }

        $account = $user->payoutAccounts()->create($request->validated());

        return $this->createdResponse(
            'Payout account added successfully',
            new PayoutAccountResource($account)
        );
    }
}
