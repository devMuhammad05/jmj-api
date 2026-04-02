<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Filament\Admin\Resources\Pools\PoolResource;
use App\Models\MetaTraderCredential;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPool extends EditRecord
{
    protected static string $resource = PoolResource::class;

    private array $metaTraderData = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $credential = $this->record->metaTraderCredential;

        if ($credential) {
            $data['mt_account_number'] = $credential->mt_account_number;
            $data['mt_server'] = $credential->mt_server;
            $data['platform_type'] = $credential->platform_type?->value;
            $data['risk_level'] = $credential->risk_level?->value;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
    {
        if (filled($this->metaTraderData['mt_account_number'] ?? null)) {
            MetaTraderCredential::updateOrCreate(
                ['pool_id' => $this->record->id],
                $this->metaTraderData,
            );
        }
    }
}
