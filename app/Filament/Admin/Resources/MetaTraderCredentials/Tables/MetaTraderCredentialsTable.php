<?php

namespace App\Filament\Admin\Resources\MetaTraderCredentials\Tables;

use App\Enums\MetaTraderPlatformType;
use App\Enums\RiskLevel;
use App\Models\MetaTraderCredential;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MetaTraderCredentialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                TextColumn::make('platform_type')
                    ->label('Platform')
                    ->badge()
                    ->color(
                        fn (MetaTraderPlatformType $state): string => match (
                            $state
                        ) {
                            MetaTraderPlatformType::MT4 => 'info',
                            MetaTraderPlatformType::MT5 => 'success',
                        },
                    )
                    ->sortable(),
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
                TextColumn::make('created_at')
                    ->label('Connected')
                    ->dateTime()
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
                SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
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
                        // Log credential access for audit trail
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'log_type' => \App\Enums\LogType::MT->value,
                                'action' => 'view_credentials',
                                'mt_account_number' => $record->mt_account_number,
                                'user_email' => $record->user->email,
                                'accessed_at' => now()->toDateTimeString(),
                            ])
                            ->log('MetaTrader credentials accessed');

                        Notification::make()
                            ->title('Credentials Accessed')
                            ->body(
                                'This action has been logged for security purposes.',
                            )
                            ->info()
                            ->send();
                    }),
                // EditAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }
}
