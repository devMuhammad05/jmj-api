<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\MetaTraderData;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConnectMetaTraderService
{
    /**
     * Provision a MetaTrader account via the fast backend.
     */
    public function provision(User $user, MetaTraderData $data): Response
    {
        $baseUrl = config('services.fast_backend.base_url');

        Log::info('MetaTrader provision request', ['url' => "{$baseUrl}/provision-account"]);

        return Http::baseUrl($baseUrl)
            ->post('/provision-account', [
                'user_id' => $user->id,
                'name' => $user->full_name,
                'login' => $data->mt_account_number,
                'password' => $data->mt_password,
                'server' => $data->mt_server,
                'risk_level' => $data->risk_level,
                'platform' => 'mt5',
                'magic' => 0,
                'pool_id' => $data->pool_id,
            ]);
    }
}
