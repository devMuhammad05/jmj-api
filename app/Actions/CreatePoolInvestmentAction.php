<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\PoolInvestmentData;
use App\Enums\PoolInvestmentStatus;
use App\Models\MetaTraderCredential;
use App\Models\PoolInvestment;
use App\Models\User;

class CreatePoolInvestmentAction
{
    public function execute(User $user, PoolInvestmentData $data): PoolInvestment
    {
        $investment = $user->poolInvestments()->create([
            'pool_id' => $data->pool_id,
            'full_name' => $data->full_name,
            'phone_number' => $data->phone_number,
            'bank_name' => $data->bank_name,
            'account_number' => $data->account_number,
            'account_name' => $data->account_name,
            'contribution' => $data->contribution,
            'payment_proof_path' => $data->payment_proof_path,
            'terms_accepted' => $data->terms_accepted,
            'status' => PoolInvestmentStatus::PENDING,
            'share_percentage' => 0, // Will be calculated after verification
        ]);

        if (filled($data->mt_account_number)) {
            MetaTraderCredential::create([
                'user_id' => $user->id,
                'pool_investment_id' => $investment->id,
                'mt_account_number' => $data->mt_account_number,
                'mt_password' => $data->mt_password,
                'mt_server' => $data->mt_server,
                'platform_type' => $data->platform_type,
                'initial_deposit' => $data->initial_deposit,
                'risk_level' => $data->risk_level,
            ]);
        }

        return $investment;
    }
}
