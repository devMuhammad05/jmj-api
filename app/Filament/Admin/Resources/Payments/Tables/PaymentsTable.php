<?php

namespace App\Filament\Admin\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('reference')
                //     ->label('Reference')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                // TextColumn::make('plan.name')
                //     ->label('Plan')
                //     ->searchable()
                //     ->sortable(),

                TextColumn::make('gateway.name')
                    ->label('Gateway')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (PaymentType $state): string => format_status_text($state->value))
                    ->badge()
                    ->color(fn (PaymentType $state): string => match ($state) {
                        PaymentType::PoolInvestment => 'info',
                        PaymentType::MetaCredential => 'warning',
                        PaymentType::ClassSubscription => 'success',
                        PaymentType::Signals => 'primary',
                    })
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->formatStateUsing(fn (PaymentStatus $state): string => format_status_text($state->value))
                    ->badge()
                    ->color(fn (PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Pending => 'gray',
                        PaymentStatus::Approved => 'success',
                        PaymentStatus::Rejected => 'danger',
                        PaymentStatus::Failed => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(PaymentStatus::class)
                    ->label('Status'),
                SelectFilter::make('type')
                    ->options(PaymentType::class)
                    ->label('Type'),
            ])
            ->recordActions([
                Action::make('view_proof')
                    ->label('View Proof')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->color('gray')
                    ->modalHeading('Payment Proof')
                    ->modalContent(fn (Payment $record): HtmlString => $record->proofs->isNotEmpty()
                        ? new HtmlString(
                            $record->proofs->map(fn ($proof): string => sprintf(
                                '<a href="%s" target="_blank" rel="noopener noreferrer"><img src="%s" alt="Payment Proof" style="max-width: 100%%; max-height: 700px; margin-bottom: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: block;"></a>',
                                e($proof->payment_proof_url),
                                e($proof->payment_proof_url),
                            ))->join('')
                        )
                        : new HtmlString('<p class="text-sm text-gray-500">No proof submitted for this payment.</p>')
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ]);
    }
}
