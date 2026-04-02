<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Filament\Admin\Resources\Pools\PoolResource;
use App\Models\MetaTraderCredential;
use Filament\Resources\Pages\CreateRecord;

class CreatePool extends CreateRecord
{
    protected static string $resource = PoolResource::class;

    private array $metaTraderData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->metaTraderData = array_filter([
            'mt_account_number' => $data['mt_account_number'] ?? null,
            'mt_password' => $data['mt_password'] ?? null,
            'mt_server' => $data['mt_server'] ?? null,
            'platform_type' => $data['platform_type'] ?? null,
            'risk_level' => $data['risk_level'] ?? null,
        ]);

        unset(
            $data['mt_account_number'],
            $data['mt_password'],
            $data['mt_server'],
            $data['platform_type'],
            $data['risk_level'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        if (filled($this->metaTraderData['mt_account_number'] ?? null)) {
            MetaTraderCredential::create([
                'pool_id' => $this->record->id,
                ...$this->metaTraderData,
            ]);
        }
    }
}
