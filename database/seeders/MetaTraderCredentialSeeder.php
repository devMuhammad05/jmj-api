<?php

namespace Database\Seeders;

use App\Enums\MetaTraderPlatformType;
use App\Enums\RiskLevel;
use App\Models\MetaTraderCredential;
use App\Models\User;
use Illuminate\Database\Seeder;

class MetaTraderCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get BugJam user
        $bugJam = User::where('email', 'bigjam@gmail.com')->first();

        if ($bugJam) {
            MetaTraderCredential::create([
                'user_id' => $bugJam->id,
                'mt_account_number' => '12345678',
                'mt_password' => 'BugJam@MT5Pass',
                'mt_server' => 'Exness-MT5Real',
                'platform_type' => MetaTraderPlatformType::MT5,
                'initial_deposit' => 5000.00,
                'risk_level' => RiskLevel::Medium,
            ]);
        }
    }
}
