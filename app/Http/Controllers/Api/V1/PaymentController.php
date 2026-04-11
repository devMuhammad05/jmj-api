<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    /**
     * List authenticated user's payments.
     */
    public function index(Request $request): JsonResponse
    {
        $payments = $request->user()
            ->payments()
            ->with(['plan', 'gateway', 'proofs'])
            ->latest()
            ->paginate(10);

        return $this->successResponse('Payments retrieved successfully', PaymentResource::collection($payments));
    }

    /**
     * Show a single payment.
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== auth()->id()) {
            return $this->forbiddenResponse();
        }

        $payment->load(['plan', 'gateway', 'proofs']);

        return $this->successResponse('Payment retrieved successfully', new PaymentResource($payment));
    }
}
