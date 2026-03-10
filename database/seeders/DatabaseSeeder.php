<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create users
        $users = [
            [
                "full_name" => "Muhammad",
                "email" => "muhammad@gmail.com",
                "phone_number" => "+1234567890",
                "country" => "Nigeria",
                "email_verified_at" => now(),
                "password" => Hash::make("password123"),
            ],
            [
                "full_name" => "BigJam",
                "email" => "bigjam@gmail.com",
                "phone_number" => "+1234567891",
                "country" => "Nigeria",
                "email_verified_at" => now(),
                "password" => Hash::make("password123"),
            ],
            [
                "full_name" => "Hameed",
                "email" => "hameed@gmail.com",
                "phone_number" => "+1234567892",
                "country" => "Nigeria",
                "email_verified_at" => now(),
                "password" => Hash::make("password123"),
            ],
            [
                "full_name" => "Raji codes",
                "email" => "rajicodes@gmail.com",
                "phone_number" => "+1234567893",
                "country" => "Nigeria",
                "email_verified_at" => now(),
                "password" => Hash::make("password123"),
            ],
            [
                "full_name" => "Administrator",
                "email" => "admin@jmj.com",
                "phone_number" => "+1234567894",
                "country" => "Nigeria",
                "email_verified_at" => now(),
                "role" => Role::Admin->value,
                "password" => Hash::make("jmjapp1234@"),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Seed verifications
        $this->call([
            VerificationSeeder::class,
            MetaTraderCredentialSeeder::class,
            SignalSeeder::class,
            PoolInvestmentSeeder::class,
            TradingClassSeeder::class,
        ]);
    }
}
