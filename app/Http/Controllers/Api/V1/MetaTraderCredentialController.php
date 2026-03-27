<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\MetaTraderData;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMetaTraderCredentialRequest;
use App\Services\ConnectMetaTraderService;
use Illuminate\Http\JsonResponse;

class MetaTraderCredentialController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetaTraderCredentialRequest $request, ConnectMetaTraderService $connectMetaTrader): JsonResponse
    {
        $data = MetaTraderData::fromRequest($request);

        $response = $connectMetaTrader->provision($request->user(), $data);

        if (! $response->successful()) {
            return $this->errorResponse(
                $response->json('message') ?? 'Failed to connect MetaTrader account',
                $response->status(),
            );
        }

        return $this->createdResponse(
            'MetaTrader credentials saved successfully'
        );
    }
}
