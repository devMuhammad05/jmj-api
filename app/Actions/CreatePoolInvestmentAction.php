<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\PoolInvestmentData;
use App\Enums\PaymentStatus;
use App\Enums\PoolInvestmentStatus;
use App\Models\MetaTraderCredential;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\PoolInvestment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreatePoolInvestmentAction
{
    public function execute(User $user, PoolInvestmentData $data): PoolInvestment
    {
        return DB::transaction(function () use ($user, $data) {
            $investment = $user->poolInvestments()->create([
                'pool_id' => $data->pool_id,
                'full_name' => $data->full_name,
                'phone_number' => $data->phone_number,
                'bank_name' => $data->bank_name,
                'account_number' => $data->account_number,
                'account_name' => $data->account_name,
                'contribution' => $data->contribution,
                'amount_paid' => $data->amount_paid,
                'terms_accepted' => $data->terms_accepted,
                'status' => PoolInvestmentStatus::PENDING,
                'share_percentage' => 0, // Will be calculated after verification
            ]);

            // Create a Payment record for this investment
            $payment = Payment::create([
                'user_id' => $user->id,
                'pool_investment_id' => $investment->id,
                'payment_gateway_id' => $data->payment_gateway_id,
                'amount' => $data->amount_paid,
                'status' => PaymentStatus::Pending,
                'type' => 'pool_investment',
            ]);

            // Create the proof for the payment
            PaymentProof::create([
                'payment_id' => $payment->id,
                'payment_proof_url' => $data->payment_proof_url,
            ]);

            if (filled($data->mt_account_number)) {
                MetaTraderCredential::create([
                    'user_id' => $user->id,
                    'pool_id' => $data->pool_id,
                    'mt_account_number' => $data->mt_account_number,
                    'mt_password' => $data->mt_password,
                    'mt_server' => $data->mt_server,
                    'platform_type' => $data->platform_type,
                    'initial_deposit' => $data->initial_deposit,
                    'risk_level' => $data->risk_level,
                ]);
            }

            return $investment;
        });
    }
}
