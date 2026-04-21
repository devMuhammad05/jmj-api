<?php

namespace Database\Seeders;

use App\Enums\MetaTraderPlatformType;
use App\Enums\PaymentStatus;
use App\Enums\RiskLevel;
use App\Models\MetaTraderCredential;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\PaymentProof;
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
            $credential = MetaTraderCredential::create([
                'user_id' => $bugJam->id,
                'mt_account_number' => '12345678',
                'mt_password' => 'BugJam@MT5Pass',
                'mt_server' => 'Exness-MT5Real',
                'platform_type' => MetaTraderPlatformType::MT5,
                'initial_deposit' => 5000.00,
                'risk_level' => RiskLevel::MODERATE->value,
            ]);

            // Get first payment gateway
            $gateway = PaymentGateway::first();

            if ($gateway) {
                $payment = Payment::create([
                    'user_id' => $bugJam->id,
                    'meta_trader_credential_id' => $credential->id,
                    'payment_gateway_id' => $gateway->id,
                    'amount' => 5000.00,
                    'status' => PaymentStatus::Approved,
                    'type' => 'meta_trader_credential',
                ]);

                PaymentProof::create([
                    'payment_id' => $payment->id,
                    'payment_proof_url' => 'https://example.com/proof.jpg',
                ]);
            }
        }
    }
}
