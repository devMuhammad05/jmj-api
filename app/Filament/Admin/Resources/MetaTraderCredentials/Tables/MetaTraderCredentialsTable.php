<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Tables;

use App\Enums\MetaTraderCredentialConnectionStatus;
use App\Enums\MetaTraderPlatformType;
use App\Enums\RiskLevel;
use App\Jobs\ConnectMetaTraderAccount;
use App\Models\MetaTraderCredential;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class MetaTraderCredentialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn () => MetaTraderCredential::with(['payment.gateway', 'payment.proofs']))
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('mt_account_number')
                    ->label('MT Account')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('mt_server')
                    ->label('Server')
                    ->searchable()
                    ->toggleable(),
                // TextColumn::make('platform_type')
                //     ->label('Platform')
                //     ->badge()
                //     ->color(
                //         fn (MetaTraderPlatformType $state): string => match (
                //             $state
                //         ) {
                //             MetaTraderPlatformType::MT4 => 'info',
                //             MetaTraderPlatformType::MT5 => 'success',
                //         },
                //     )
                //     ->sortable(),
                TextColumn::make('risk_level')
                    ->label('Risk Level')
                    ->badge()
                    ->color(
                        fn (RiskLevel $state): string => match ($state) {
                            RiskLevel::CONSERVATIVE => 'success',
                            RiskLevel::MODERATE => 'warning',
                        },
                    )
                    ->sortable(),
                TextColumn::make('initial_deposit')
                    ->label('Initial Deposit')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('payment.amount')
                    ->label('Amount Paid')
                    ->money('USD')
                    ->sortable(),
                // TextColumn::make('payment.status')
                //     ->label('Payment Status')
                //     ->formatStateUsing(fn (string $state): string => format_status_text($state))
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'pending' => 'warning',
                //         'submitted' => 'info',
                //         'under_review' => 'primary',
                //         'approved' => 'success',
                //         'rejected' => 'danger',
                //         'failed' => 'danger',
                //         default => 'gray',
                //     }),

                TextColumn::make('status')
                    ->label('Account Status')
                    ->badge()
                    ->color(
                        fn (MetaTraderCredentialConnectionStatus $state): string => match ($state) {
                            MetaTraderCredentialConnectionStatus::Pending => 'warning',
                            MetaTraderCredentialConnectionStatus::Connected => 'success',
                        },
                    )
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('platform_type')
                    ->label('Platform')
                    ->options(MetaTraderPlatformType::class),
                SelectFilter::make('risk_level')
                    ->label('Risk Level')
                    ->options(RiskLevel::class),
                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('payment', function ($q) use ($data) {
                                $q->where('status', $data['value']);
                            });
                        }
                    }),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('connect_account')
                    ->label('Connect Account')
                    ->icon('heroicon-o-signal')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Connect MetaTrader Account')
                    ->modalDescription('Connecting this account means you have confirmed their service charge fee. The account will be connected to the MetaAPI Cloud server (https://metaapi.cloud/).')
                    ->modalSubmitActionLabel('Yes, Connect Account')
                    ->hidden(fn (MetaTraderCredential $record): bool => $record->status === MetaTraderCredentialConnectionStatus::Connected)
                    ->action(function (MetaTraderCredential $record) {
                        ConnectMetaTraderAccount::dispatch($record->user, $record);

                        $record->save();
                        Notification::make()
                            ->title('Connecting Account')
                            ->body('The MetaTrader account connection has been queued and will be processed shortly.')
                            ->success()
                            ->send();
                    }),
                Action::make('view_credentials')
                    ->label('View Credentials')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('MetaTrader Credentials')
                    ->modalDescription(
                        'These credentials are sensitive. Only access them when necessary for trade execution.',
                    )
                    ->modalContent(
                        fn (MetaTraderCredential $record) => view(
                            'filament.admin.mt-credentials',
                            ['record' => $record],
                        ),
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->action(function (MetaTraderCredential $record) {
                        Notification::make()
                            ->title('Credentials Accessed')
                            ->body(
                                'This action has been logged for security purposes.',
                            )
                            ->info()
                            ->send();
                    }),
                Action::make('view_payment')
                    ->label('View Payment')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->color('info')
                    ->modalHeading('Payment Proof')
                    ->modalContent(fn (MetaTraderCredential $record): HtmlString => $record->payment?->proofs?->isNotEmpty()
                        ? new HtmlString(
                            $record->payment->proofs->map(fn ($proof): string => sprintf(
                                '<a href="%s" target="_blank" rel="noopener noreferrer"><img src="%s" alt="Payment Proof" style="max-width: 100%%; max-height: 700px; margin-bottom: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: block;"></a>',
                                e($proof->payment_proof_url),
                                e($proof->payment_proof_url),
                            ))->join('')
                        )
                        : new HtmlString('<p class="text-sm text-gray-500">No proof submitted for this payment.</p>')
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (MetaTraderCredential $record): bool => $record->payment !== null),

                // EditAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }
}
