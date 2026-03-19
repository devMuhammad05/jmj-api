<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\MetaTraderData;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMetaTraderCredentialRequest;
use App\Jobs\ConnectMetaTraderAccount;
use Illuminate\Http\JsonResponse;

class MetaTraderCredentialController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetaTraderCredentialRequest $request): JsonResponse
    {
        $data = MetaTraderData::fromRequest($request);

        ConnectMetaTraderAccount::dispatch($request->user(), $data);

        return $this->createdResponse(
            'MetaTrader credentials saved successfully'
        );
    }
}
