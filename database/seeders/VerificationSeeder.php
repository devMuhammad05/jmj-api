<?php

namespace Database\Seeders;

use App\Enums\IdType;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Seeder;

class VerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get BugJam user
        $bugJam = User::where('email', 'bugjam@jmj.com')->first();

        if ($bugJam) {
            Verification::create([
                'user_id' => $bugJam->id,
                'id_type' => IdType::Passport,
                'id_number' => 'A12345678',
                'id_card_front_img_url' => 'https://via.placeholder.com/800x600/4CAF50/FFFFFF?text=Passport+Front',
                'id_card_back_img_url' => 'https://via.placeholder.com/800x600/2196F3/FFFFFF?text=Passport+Back',
                'selfie_img_url' => 'https://via.placeholder.com/600x800/FF9800/FFFFFF?text=Selfie+with+ID',
                'status' => VerificationStatus::PENDING,
            ]);
        }
    }
}
