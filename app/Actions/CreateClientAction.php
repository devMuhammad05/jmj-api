<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ClientData;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateClientAction
{
    public function __construct(
        private CreateMetaTraderCredentialAction $createMetaTrader,
    ) {}

    public function execute(User $owner, ClientData $data): Client
    {
        return DB::transaction(function () use ($owner, $data) {
            $client = Client::create([
                'user_id' => $owner->id,
                'full_name' => $data->full_name,
                'email' => $data->email,
                'phone' => $data->phone,
                'client_id' => 'CL-'.mt_rand(10000000, 99999999),
            ]);

            $this->createMetaTrader->execute($client, $data->metaTrader);

            return $client;
        });
    }
}
