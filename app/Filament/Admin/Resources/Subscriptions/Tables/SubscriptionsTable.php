<?php

namespace App\Filament\Admin\Resources\Subscriptions\Tables;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->sortable(),

                TextColumn::make('plan.price')
                    ->label('Price')
                    ->money('USD'),

                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('ends_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->ends_at?->isPast() ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Subscription $record): SubscriptionStatus => $record->status)
                    ->formatStateUsing(fn (SubscriptionStatus $state): string => $state->label())
                    ->color(fn (Subscription $record): string => $record->status->color()),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(
                        collect(SubscriptionStatus::cases())
                            ->mapWithKeys(fn (SubscriptionStatus $s) => [$s->value => $s->label()])
                            ->all()
                    )
                    ->query(fn ($query, array $data) => match ($data['value'] ?? null) {
                        'pending' => $query->whereNull('starts_at')->where('is_active', false),
                        'active' => $query->where('is_active', true)->whereNotNull('ends_at')->where('ends_at', '>', now()),
                        'expired' => $query->whereNotNull('ends_at')->where('ends_at', '<', now()),
                        'inactive' => $query->where('is_active', false)->whereNotNull('starts_at'),
                        default => $query,
                    }),

                SelectFilter::make('plan')->relationship('plan', 'name'),
            ])
            ->recordActions([
                Action::make('view_proof')
                    ->label('View Proof')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->color('gray')
                    ->modalHeading('Payment Proof')
                    ->modalContent(fn (Subscription $record): HtmlString => $record->payment?->proofs?->isNotEmpty()
                        ? new HtmlString(
                            $record->payment->proofs->map(fn ($proof): string => sprintf(
                                '<a href="%s" target="_blank" rel="noopener noreferrer"><img src="%s" alt="Payment Proof" style="max-width: 100%%; max-height: 700px; margin-bottom: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: block;"></a>',
                                e($proof->payment_proof_url),
                                e($proof->payment_proof_url),
                            ))->join('')
                        )
                        : new HtmlString('<p class="text-sm text-gray-500">No proof submitted.</p>')
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (Subscription $record): bool => $record->payment_id !== null),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
