<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateClientAction;
use App\DTOs\ClientData;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\V1\StoreClientRequest;
use App\Http\Resources\V1\ClientResource;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class ClientController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $clients = Client::query()
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            'Clients listed successfully',
            ClientResource::collection($clients)
        );
    }

    /**
     * Store a newly created resource in storage (client, verification, metatrader).
     */
    public function store(StoreClientRequest $request, CreateClientAction $createClientAction): JsonResponse
    {
        $client = $createClientAction->execute(
            owner: $request->user(),
            data: ClientData::fromRequest($request)
        );

        return $this->createdResponse(
            'Client and related records created successfully',
            new ClientResource($client)
        );
    }
}
