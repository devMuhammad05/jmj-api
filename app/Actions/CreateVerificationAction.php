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
            'id_card_front_img' => $data->id_card_front_img,
            'id_card_back_img' => $data->id_card_back_img,
            'selfie_img' => $data->selfie_img,
        ]);
    }
}
