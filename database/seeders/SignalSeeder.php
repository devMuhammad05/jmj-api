<?php

namespace Database\Seeders;

use App\Enums\SignalAction;
use App\Enums\SignalStatus;
use App\Enums\SignalType;
use App\Models\Signal;
use Illuminate\Database\Seeder;

class SignalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $signals = [
            // Active Signals
            [
                'symbol' => 'EURUSD',
                'action' => SignalAction::BUY,
                'type' => SignalType::FREE,
                'entry_price' => 1.08500,
                'stop_loss' => 1.08200,
                'take_profit_1' => 1.09000,
                'take_profit_2' => 1.09500,
                'take_profit_3' => 1.10000,
                'status' => SignalStatus::ACTIVE,
                'pips_result' => 0,
                'notes' => 'Strong bullish momentum on H4 timeframe. Watch for breakout above resistance.',
                'is_published' => true,
            ],
            [
                'symbol' => 'GBPUSD',
                'action' => SignalAction::SELL,
                'type' => SignalType::PREMIUM,
                'entry_price' => 1.27500,
                'stop_loss' => 1.27800,
                'take_profit_1' => 1.27000,
                'take_profit_2' => 1.26500,
                'take_profit_3' => null,
                'status' => SignalStatus::ACTIVE,
                'pips_result' => 0,
                'notes' => 'Bearish divergence on RSI. Expecting pullback to support level.',
                'is_published' => true,
            ],
            [
                'symbol' => 'XAUUSD',
                'action' => SignalAction::BUY_LIMIT,
                'type' => SignalType::FREE,
                'entry_price' => 2050.00,
                'stop_loss' => 2040.00,
                'take_profit_1' => 2070.00,
                'take_profit_2' => 2080.00,
                'take_profit_3' => 2090.00,
                'status' => SignalStatus::ACTIVE,
                'pips_result' => 0,
                'notes' => 'Gold showing strong support at 2050. Buy on dip strategy.',
                'is_published' => true,
            ],
            
            // Completed Signals - Hit TP
            [
                'symbol' => 'USDJPY',
                'action' => SignalAction::BUY,
                'type' => SignalType::FREE,
                'entry_price' => 148.500,
                'stop_loss' => 148.200,
                'take_profit_1' => 149.000,
                'take_profit_2' => 149.500,
                'take_profit_3' => null,
                'status' => SignalStatus::TP,
                'pips_result' => 50.00,
                'notes' => 'Perfect execution. Hit TP1 within 4 hours.',
                'is_published' => true,
                'created_at' => now()->subDays(2),
            ],
            [
                'symbol' => 'AUDUSD',
                'action' => SignalAction::SELL,
                'type' => SignalType::PREMIUM,
                'entry_price' => 0.66500,
                'stop_loss' => 0.66800,
                'take_profit_1' => 0.66000,
                'take_profit_2' => 0.65500,
                'take_profit_3' => null,
                'status' => SignalStatus::TP,
                'pips_result' => 50.00,
                'notes' => 'Strong bearish trend continuation.',
                'is_published' => true,
                'created_at' => now()->subDays(3),
            ],
            
            // Completed Signals - Hit SL
            [
                'symbol' => 'EURUSD',
                'action' => SignalAction::SELL,
                'type' => SignalType::FREE,
                'entry_price' => 1.08000,
                'stop_loss' => 1.08300,
                'take_profit_1' => 1.07500,
                'take_profit_2' => null,
                'take_profit_3' => null,
                'status' => SignalStatus::SL,
                'pips_result' => -30.00,
                'notes' => 'Market reversed unexpectedly due to news event.',
                'is_published' => true,
                'created_at' => now()->subDays(5),
            ],
            
            // Cancelled Signal
            [
                'symbol' => 'BTCUSD',
                'action' => SignalAction::BUY,
                'type' => SignalType::PREMIUM,
                'entry_price' => 45000.00,
                'stop_loss' => 44500.00,
                'take_profit_1' => 46000.00,
                'take_profit_2' => 47000.00,
                'take_profit_3' => null,
                'status' => SignalStatus::CANCELLED,
                'pips_result' => 0,
                'notes' => 'Market conditions changed. Signal cancelled before entry.',
                'is_published' => true,
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($signals as $signalData) {
            Signal::create($signalData);
        }
    }
}
