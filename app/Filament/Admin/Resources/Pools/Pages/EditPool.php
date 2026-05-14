<?php

namespace App\Filament\Admin\Resources\Pools\Pages;

use App\Enums\MetaTraderCredentialConnectionStatus;
use App\Enums\RiskLevel;
use App\Filament\Admin\Resources\Pools\PoolResource;
use App\Jobs\ConnectMetaTraderAccount;
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
        if (! filled($this->metaTraderData['mt_account_number'] ?? null)) {
            return;
        }

        $riskLevel = $this->metaTraderData['risk_level'] instanceof RiskLevel
            ? $this->metaTraderData['risk_level']->value
            : $this->metaTraderData['risk_level'];

        $credentialData = [
            'mt_account_number' => $this->metaTraderData['mt_account_number'],
            'mt_server' => $this->metaTraderData['mt_server'],
            'platform_type' => $this->metaTraderData['platform_type'] ?? null,
            'risk_level' => $riskLevel,
            'status' => MetaTraderCredentialConnectionStatus::Pending,
        ];

        if (filled($this->metaTraderData['mt_password'] ?? null)) {
            $credentialData['mt_password'] = $this->metaTraderData['mt_password'];
        }

        $credential = $this->record->metaTraderCredential
            ? tap($this->record->metaTraderCredential)->update($credentialData)
            : MetaTraderCredential::create(array_merge($credentialData, [
                'user_id' => auth()->id(),
                'pool_id' => $this->record->id,
                'initial_deposit' => 0.0,
            ]));

        ConnectMetaTraderAccount::dispatch(auth()->user(), $credential);
    }
}
