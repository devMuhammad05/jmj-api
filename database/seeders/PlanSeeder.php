<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Signal;
use App\Models\TradingClass;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Free plan — access to free signals only, no classes
        $free = Plan::firstOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'price' => 0.00,
                'duration_days' => 36500,
                'is_active' => true,
                'level' => 1,
            ],
        );

        $pro = Plan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 29.99,
                'duration_days' => 30,
                'is_active' => true,
                'level' => 2,
            ],
        );

        $vip = Plan::firstOrCreate(
            ['slug' => 'vip'],
            [
                'name' => 'VIP',
                'price' => 199.99,
                'duration_days' => 365,
                'is_active' => true,
                'level' => 3,
            ],
        );

        $allSignals = Signal::all();
        $allClasses = TradingClass::all();

        // Free plan: no signals by default (assign manually per signal)
        // Pro & VIP: all signals + all trading classes
        $pro->signals()->syncWithoutDetaching($allSignals->pluck('id')->toArray());
        $pro->tradingClasses()->syncWithoutDetaching($allClasses->pluck('id')->toArray());

        $vip->signals()->syncWithoutDetaching($allSignals->pluck('id')->toArray());
        $vip->tradingClasses()->syncWithoutDetaching($allClasses->pluck('id')->toArray());
    }
}
