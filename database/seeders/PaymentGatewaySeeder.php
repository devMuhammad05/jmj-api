<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::updateOrCreate(
            ['code' => 'usdt_trc20'],
            [
                'name' => 'USDT TRC20',
                'wallet_address' => 'TRthwAMM8oaWzHnbUw1zaXgVeXaonhmQq9',
                'network' => 'TRC20',
                'bar_code_path' => 'img/payment-gateway/usdt_qr_code.jpg',
                'is_active' => true,
            ],
        );

        PaymentGateway::updateOrCreate(
            ['code' => 'usdt_erc20'],
            [
                'name' => 'USDT ERC20',
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'network' => 'ERC20',
                'is_active' => true,
            ],
        );

        PaymentGateway::updateOrCreate(
            ['code' => 'btc'],
            [
                'name' => 'Bitcoin',
                'wallet_address' => 'bc1qup697nu0r7y5rgh3qqd08k96xyjfjslvusk5qf',
                'network' => 'BTC',
                'bar_code_path' => 'img/payment-gateway/btc_qr_code.jpg',
                'is_active' => true,
            ],
        );

        PaymentGateway::updateOrCreate(
            ['code' => 'sol'],
            [
                'name' => 'Solana',
                'wallet_address' => 'j64gBCpMHsTuDhVc8SKvWUk4jgABuo11DLCRzggK8cv',
                'network' => 'SOL',
                'bar_code_path' => 'img/payment-gateway/sol_qr_code.jpg',
                'is_active' => true,
            ],
        );
    }
}
