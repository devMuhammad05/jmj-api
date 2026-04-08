<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Requests\Api\V1\UploadPaymentProofRequest;
use App\Http\Resources\V1\PaymentGatewayResource;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * Initiate a new payment.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $gateway = PaymentGateway::where('code', $request->gateway_code)
            ->where('is_active', true)
            ->firstOrFail();

        $payment = $request->user()->payments()->create([
            'plan_id'            => $request->plan_id,
            'payment_gateway_id' => $gateway->id,
            'amount'             => $request->amount,
            'status'             => PaymentStatus::Pending,
            'reference'          => $request->reference,
            'transaction_id'     => 'TXN-' . strtoupper(uniqid()),
        ]);

        $payment->load(['plan', 'gateway']);

        return $this->createdResponse('Payment initiated successfully', new PaymentResource($payment));
    }

    /**
     * Show a single payment.
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        Gate::authorize('view', $payment);

        $payment->load(['plan', 'gateway', 'proofs']);

        return $this->successResponse('Payment retrieved successfully', new PaymentResource($payment));
    }

    /**
     * Upload proof of payment.
     */
    public function uploadProof(UploadPaymentProofRequest $request, Payment $payment): JsonResponse
    {
        Gate::authorize('view', $payment);

        $path = $request->file('proof')->store('payment-proofs', 'public');

        $payment->proofs()->create(['payment_proof_url' => $path]);

        $payment->update(['status' => PaymentStatus::Submitted]);

        $payment->load(['plan', 'gateway', 'proofs']);

        return $this->successResponse('Payment proof uploaded successfully', new PaymentResource($payment));
    }

    /**
     * List active payment gateways with their config (payment details).
     */
    public function gateways(): JsonResponse
    {
        $gateways = PaymentGateway::where('is_active', true)->get();

        return $this->successResponse('Payment gateways retrieved successfully', PaymentGatewayResource::collection($gateways));
    }
}
