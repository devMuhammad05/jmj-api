<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\PaymentGatewayResource;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;

class PaymentGatewayController extends ApiController
{
    public function index(): JsonResponse
    {
        $gateways = PaymentGateway::where('is_active', true)->get();

        return $this->successResponse('Payment gateways retrieved successfully', PaymentGatewayResource::collection($gateways));
    }
}
