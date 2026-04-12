<?php

namespace Database\Seeders;

use App\Enums\PlanType;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'signals-pro',
                'name' => 'Signals PRO',
                'type' => PlanType::Signals,
                'price' => 29.99,
                'duration_days' => 30,
                'level' => 2,
            ],
            [
                'slug' => 'signals-vip',
                'name' => 'Signals VIP',
                'type' => PlanType::Signals,
                'price' => 199.99,
                'duration_days' => 365,
                'level' => 3,
            ],
            [
                'slug' => 'trading-pro',
                'name' => 'Trading Classes PRO',
                'type' => PlanType::TradingClasses,
                'price' => 29.99,
                'duration_days' => 30,
                'level' => 2,
            ],
            [
                'slug' => 'trading-vip',
                'name' => 'Trading Classes VIP',
                'type' => PlanType::TradingClasses,
                'price' => 199.99,
                'duration_days' => 365,
                'level' => 3,
            ],
        ];

        foreach ($plans as $data) {
            Plan::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true]),
            );
        }
    }
}
