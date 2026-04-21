<?php

namespace App\Jobs;

use App\DTOs\MetaTraderData;
use App\Models\User;
use App\Services\ConnectMetaTraderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ConnectMetaTraderAccount implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public User $user,
        public MetaTraderData $data,
    ) {}

    public function handle(ConnectMetaTraderService $connectMetaTrader): void
    {
        $response = $connectMetaTrader->provision($this->user, $this->data);

        Log::info('MetaTrader provision response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if ($response->successful()) {
            // Create the MetaTrader credential if provisioning was successful
            $credential = $this->user->metaTraderCredentials()->create([
                'mt_account_number' => $this->data->mt_account_number,
                'mt_password' => $this->data->mt_password,
                'mt_server' => $this->data->mt_server,
                'platform_type' => \App\Enums\MetaTraderPlatformType::MT5,
                'initial_deposit' => $this->data->initial_deposit,
                'risk_level' => $this->data->risk_level,
                'pool_id' => $this->data->pool_id,
            ]);

            // Link the payment to the credential
            if ($this->data->payment_id) {
                \App\Models\Payment::where('id', $this->data->payment_id)
                    ->update(['meta_trader_credential_id' => $credential->id]);
            }
        } else {
            Log::error('Failed to connect MetaTrader account', [
                'user_id' => $this->user->id,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
                'login' => $this->data->mt_account_number,
                'server' => $this->data->mt_server,
            ]);
        }
    }
}
