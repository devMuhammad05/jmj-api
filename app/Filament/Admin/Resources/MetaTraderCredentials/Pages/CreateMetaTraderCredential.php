<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Pages;

use App\DTOs\MetaTraderData;
use App\Filament\Admin\Resources\MetaTraderCredentials\MetaTraderCredentialResource;
use App\Jobs\ConnectMetaTraderAccount;
use App\Models\MetaTraderCredential;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMetaTraderCredential extends CreateRecord
{
    protected static string $resource = MetaTraderCredentialResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::findOrFail($data['user_id']);

        ConnectMetaTraderAccount::dispatch($user, new MetaTraderData(
            mt_account_number: $data['mt_account_number'],
            mt_password: $data['mt_password'],
            mt_server: $data['mt_server'],
            initial_deposit: (float) $data['initial_deposit'],
            risk_level: $data['risk_level'] instanceof \App\Enums\RiskLevel
                ? $data['risk_level']->value
                : $data['risk_level'],
            pool_id: $data['pool_id'] ?? null,
        ));

        return new MetaTraderCredential;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
