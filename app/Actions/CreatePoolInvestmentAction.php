<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\PoolInvestmentData;
use App\Enums\PoolInvestmentStatus;
use App\Models\PoolInvestment;
use App\Models\User;

class CreatePoolInvestmentAction
{
    public function execute(User $user, PoolInvestmentData $data): PoolInvestment
    {
        return $user->poolInvestments()->create([
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
    }
}
