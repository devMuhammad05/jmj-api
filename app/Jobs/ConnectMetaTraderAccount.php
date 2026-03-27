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

        if (! $response->successful()) {
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
