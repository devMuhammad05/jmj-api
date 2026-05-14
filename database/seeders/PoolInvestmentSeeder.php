<?php

namespace Database\Seeders;

use App\Enums\PoolInvestmentStatus;
use App\Enums\PoolStatus;
use App\Models\Pool;
use App\Models\PoolInvestment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PoolInvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $pool = Pool::firstOrCreate(
            ['name' => 'Growth Pool Alpha'],
            [
                'total_amount' => 500000.00,
                'minimum_investment' => 1000.00,
                'status' => PoolStatus::ACTIVE,
            ],
        );

        $user = User::where('email', 'muhammad@gmail.com')->first();

        if (! $user) {
            return;
        }

        PoolInvestment::create([
            'user_id' => $user->id,
            'pool_id' => $pool->id,
            'full_name' => $user->full_name,
            'phone_number' => $user->phone_number ?? '+2348012345678',
            'bank_name' => 'First Bank Nigeria',
            'account_number' => '3012345678',
            'account_name' => $user->full_name,
            'contribution' => 5000.00,
            'amount_paid' => 5000.00,
            'share_percentage' => 1.0000,
            'status' => PoolInvestmentStatus::PENDING,
            'terms_accepted' => true,
        ]);
    }
}
