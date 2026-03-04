<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\MetaTraderData;
use App\Models\Client;
use App\Models\MetaTraderCredential;

class CreateMetaTraderCredentialAction
{
    public function execute(Client $client, MetaTraderData $data): MetaTraderCredential
    {
        return $client->metaTraderCredential()->create([
            'mt_account_number' => $data->mt_account_number,
            'mt_password' => $data->mt_password,
            'mt_server' => $data->mt_server,
            'initial_deposit' => $data->initial_deposit,
            'risk_level' => $data->risk_level,
        ]);
    }
}
