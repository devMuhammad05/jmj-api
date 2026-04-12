<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::firstOrCreate(
            ['code' => 'usdt_trc20'],
            [
                'name' => 'USDT TRC20',
                'wallet_address' => 'TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE',
                'network' => 'TRC20',
                'is_active' => true,
            ],
        );

        PaymentGateway::firstOrCreate(
            ['code' => 'usdt_erc20'],
            [
                'name' => 'USDT ERC20',
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'network' => 'ERC20',
                'is_active' => true,
            ],
        );
    }
}
