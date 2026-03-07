<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateVerificationAction;
use App\DTOs\VerificationData;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreVerificationRequest;
use App\Http\Resources\V1\VerificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends ApiController
{
    /**
     * Display the current user's verification status.
     */
    public function index(Request $request): JsonResponse
    {
        $verification = $request->user()->verification;

        if (! $verification) {
            return $this->notFoundResponse('Verification not found');
        }

        return $this->successResponse(
            'Verification status retrieved successfully',
            new VerificationResource($verification)
        );
    }

    /**
     * Create or update a verification for the current user.
     */
    public function store(StoreVerificationRequest $request, CreateVerificationAction $createVerification): JsonResponse
    {
        $verification = $createVerification->execute(
            $request->user(),
            VerificationData::fromRequest($request)
        );

        return $this->successResponse(
            'Verification submitted successfully',
            new VerificationResource($verification)
        );
    }
}
