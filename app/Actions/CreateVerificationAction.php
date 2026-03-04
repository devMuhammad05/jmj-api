<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\VerificationData;
use App\Models\Client;
use App\Models\Verification;

class CreateVerificationAction
{
    public function execute(Client $client, VerificationData $data): Verification
    {
        return $client->verification()->create([
            'id_type' => $data->id_type,
            'id_number' => $data->id_number,
            'id_card_front_img_url' => $data->id_card_front_img_url,
            'id_card_back_img_url' => $data->id_card_back_img_url,
            'selfie_img_url' => $data->selfie_img_url,
        ]);
    }
}
