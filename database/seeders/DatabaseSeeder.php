<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        // Admin
        DB::table('users')->insert([
            'full_name' => 'Administrator',
            'email' => 'admin@jmj.com',
            'email_verified_at' => now(),
            'role' => Role::Admin->value,
            'password' => Hash::make('jmjapp1234@'),
        ]);
    }
}
