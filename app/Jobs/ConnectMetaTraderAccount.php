<?php

namespace App\Jobs;

use App\Models\MetaTraderCredential;
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
        public MetaTraderCredential $credential,
    ) {}

    public function handle(ConnectMetaTraderService $connectMetaTrader): void
    {
        $response = $connectMetaTrader->provision($this->user, $this->credential);

        Log::info('MetaTrader provision response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        if (! $response->successful()) {
            Log::error('Failed to connect MetaTrader account', [
                'user_id' => $this->user->id,
                'meta_trader_credential_id' => $this->credential->id,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);
        }
    }
}
