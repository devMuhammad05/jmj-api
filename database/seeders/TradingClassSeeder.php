<?php

namespace Database\Seeders;

use App\Enums\ClassPlatform;
use App\Models\TradingClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TradingClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                "title" => "Advanced Trading Strategies",
                "description" =>
                    "Learn advanced techniques for swing trading and position sizing.",
                "scheduled_at" => Carbon::create(2024, 1, 15, 19, 0, 0),
                "platform" => ClassPlatform::ZOOM,
                "meeting_link" => "https://zoom.us/j/example-session-1",
                "is_published" => true,
            ],
            [
                "title" => "Market Analysis Update",
                "description" =>
                    "Weekly market analysis and upcoming economic events.",
                "scheduled_at" => Carbon::create(2024, 1, 12, 18, 0, 0),
                "platform" => ClassPlatform::TELEGRAM,
                "meeting_link" => "https://t.me/example-channel",
                "is_published" => true,
            ],
            [
                "title" => "Risk Management Workshop",
                "description" =>
                    "Essential techniques for protecting your trading capital.",
                "scheduled_at" => Carbon::create(2024, 1, 20, 10, 0, 0),
                "platform" => ClassPlatform::ZOOM,
                "meeting_link" => "https://zoom.us/j/risk-mgmt-101",
                "is_published" => true,
            ],
        ];

        foreach ($classes as $classData) {
            TradingClass::create($classData);
        }

        // Add additional varied classes using factory
        // TradingClass::factory()->count(7)->create();
    }
}
