<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\MetaTraderData;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMetaTraderCredentialRequest;
use App\Jobs\ConnectMetaTraderAccount;
use App\Models\Payment;
use App\Models\PaymentProof;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MetaTraderCredentialController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetaTraderCredentialRequest $request): JsonResponse
    {
        $data = MetaTraderData::fromRequest($request);

        $payment = DB::transaction(function () use ($request) {
            // Create a Payment record for this credential submission
            $payment = Payment::create([
                'user_id' => $request->user()->id,
                'payment_gateway_id' => $request->input('payment_gateway_id'),
                'amount' => $request->input('amount_paid'),
                'status' => PaymentStatus::Pending,
                'type' => 'meta_trader_credential',
            ]);

            // Create the proof for the payment
            PaymentProof::create([
                'payment_id' => $payment->id,
                'payment_proof_url' => $request->input('payment_proof_url'),
            ]);

            return $payment;
        });

        // Update data with payment ID
        $data = new MetaTraderData(
            mt_account_number: $data->mt_account_number,
            mt_password: $data->mt_password,
            mt_server: $data->mt_server,
            initial_deposit: $data->initial_deposit,
            risk_level: $data->risk_level,
            pool_id: $data->pool_id,
            payment_id: $payment->id,
        );

        // Dispatch job to connect the account
        ConnectMetaTraderAccount::dispatch($request->user(), $data);

        return $this->createdResponse(
            'MetaTrader credentials saved successfully. Payment verification will take 24-48 hours.'
        );
    }
}
