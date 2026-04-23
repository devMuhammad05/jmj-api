<?php

namespace Database\Seeders;

use App\Models\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['key' => 'dollar', 'value' => 1650.00],
        ];

        foreach ($rates as $rate) {
            Rate::updateOrCreate(
                ['key' => $rate['key']],
                ['value' => $rate['value']]
            );
        }
    }
}
