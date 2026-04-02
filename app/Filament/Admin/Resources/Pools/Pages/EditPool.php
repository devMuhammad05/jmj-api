<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\DTOs\MetaTraderData;
use App\Enums\RiskLevel;
use App\Filament\Admin\Resources\Pools\PoolResource;
use App\Jobs\ConnectMetaTraderAccount;
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
            ConnectMetaTraderAccount::dispatch(
                auth()->user(),
                new MetaTraderData(
                    mt_account_number: $this->metaTraderData['mt_account_number'],
                    mt_password: $this->metaTraderData['mt_password'],
                    mt_server: $this->metaTraderData['mt_server'],
                    initial_deposit: 0.0,
                    risk_level: $this->metaTraderData['risk_level'] instanceof RiskLevel
                        ? $this->metaTraderData['risk_level']->value
                        : $this->metaTraderData['risk_level'],
                    pool_id: (string) $this->record->id,
                )
            );
        }
    }
}
