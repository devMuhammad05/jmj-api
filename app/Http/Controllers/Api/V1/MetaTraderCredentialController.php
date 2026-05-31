<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\MetaTraderCredentialConnectionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreMetaTraderCredentialRequest;
use App\Models\Payment;
use App\Notifications\Admin\NewPaymentSubmittedNotification;
use App\Services\AdminService;
use App\Traits\FormatsMetaTraderServerName;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MetaTraderCredentialController extends ApiController
{
    use FormatsMetaTraderServerName;

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMetaTraderCredentialRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            $user = $request->user();

            $credential = $user->metaTraderCredentials()->create([
                'mt_account_number' => $request->input('mt_account_number'),
                'mt_password' => $request->input('mt_password'),
                // 'mt_server' => $this->formatServerName((string) $request->input('mt_server')),
                'mt_server' => $request->input('mt_server'),
                'initial_deposit' => $request->input('initial_deposit'),
                'risk_level' => $request->input('risk_level'),
                'status' => MetaTraderCredentialConnectionStatus::Pending,
            ]);

            $payment = Payment::create([
                'user_id' => $user->id,
                'meta_trader_credential_id' => $credential->id,
                'payment_gateway_id' => $request->input('payment_gateway_id'),
                'amount' => $request->input('amount_paid'),
                'type' => PaymentType::MetaCredential,
                'status' => PaymentStatus::Pending,
            ]);

            $payment->proofs()->create([
                'payment_proof_url' => $request->input('payment_proof_url'),
            ]);

            $payment->load(['user', 'gateway', 'proofs']);

            $adminService = app(AdminService::class);

            foreach ($adminService->getAdminEmails() as $email) {
                Notification::route('mail', $email)->notify(new NewPaymentSubmittedNotification($payment));
            }

            Notification::send($adminService->getAdmins(), new NewPaymentSubmittedNotification($payment));
        });

        return $this->createdResponse(
            'MetaTrader credentials saved successfully. Payment verification will take 24-48 hours.'
        );
    }
}
