<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\VerificationData;
use App\Enums\VerificationStatus;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\Admin\KycSubmittedNotification;
use App\Services\AdminService;
use Illuminate\Support\Facades\Notification;

class CreateVerificationAction
{
    public function execute(User $user, VerificationData $data): Verification
    {
        $verification = $user->verification()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'id_type' => $data->id_type,
                'id_number' => $data->id_number,
                'id_card_front_img_url' => $data->id_card_front_img_url,
                'id_card_back_img_url' => $data->id_card_back_img_url,
                'selfie_img_url' => $data->selfie_img_url,
                'status' => VerificationStatus::PENDING,
            ]
        );

        $adminService = app(AdminService::class);

        foreach ($adminService->getAdminEmails() as $email) {
            Notification::route('mail', $email)->notify(new KycSubmittedNotification($verification));
        }

        return $verification;
    }
}
