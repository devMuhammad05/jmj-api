<?php

namespace App\Filament\Admin\Resources\PoolInvestments\Tables;

use App\Enums\PoolInvestmentStatus;
use App\Models\PoolInvestment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PoolInvestmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pool.name')
                    ->label('Pool')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('Investor Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contribution')
                    ->label('Contribution')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('share_percentage')
                    ->label('Share')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (PoolInvestmentStatus $state): string => match (
                            $state
                        ) {
                            PoolInvestmentStatus::PENDING => 'warning',
                            PoolInvestmentStatus::VERIFIED => 'success',
                            PoolInvestmentStatus::REJECTED => 'danger',
                        },
                    )
                    ->searchable(),

                IconColumn::make('terms_accepted')->label('Terms')->boolean(),

                // TextColumn::make('verified_at')
                //     ->label('Verified At')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('viewPaymentProof')
                    ->label('View Proof')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->color('info')
                    ->modalHeading('Payment Proof')
                    ->modalDescription(
                        fn (
                            PoolInvestment $record,
                        ): string => "Submitted by {$record->full_name}",
                    )
                    ->modalContent(
                        fn (
                            PoolInvestment $record,
                        ): HtmlString => new HtmlString(
                            '<div class="flex items-center justify-center p-4">'.
                                '<img src="'.
                                e($record->payment_proof_path).
                                '" '.
                                'alt="Payment Proof" '.
                                'class="max-w-full max-h-[70vh] rounded-xl shadow-lg object-contain" '.
                                'onerror="this.replaceWith(document.createTextNode(\'Image could not be loaded.\'))" '.
                                '/>'.
                                '</div>',
                        ),
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->slideOver()
                    ->visible(
                        fn (PoolInvestment $record): bool => filled(
                            $record->payment_proof_path,
                        ),
                    ),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
