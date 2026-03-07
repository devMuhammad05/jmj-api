<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMetaTraderCredentialRequest;
use Illuminate\Http\JsonResponse;

class MetaTraderCredentialController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetaTraderCredentialRequest $request): JsonResponse
    {
        $credential = $request->user()->metaTraderCredentials()->create(
            $request->validated()
        );

        return $this->createdResponse(
            'MetaTrader credentials saved successfully'
        );
    }
}
