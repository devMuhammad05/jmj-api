<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Enums\Role;
use App\Enums\VerificationStatus;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('role', Role::User)->with('verification'))
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('country')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('verification.status')
                    ->label('KYC Status')
                    ->badge()
                    ->default('Not Submitted')
                    ->sortable(),
                // TextColumn::make('metaTraderCredentials_count')
                //     ->label('MT Accounts')
                //     ->counts('metaTraderCredentials')
                //     ->badge()
                //     ->color('info'),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // SelectFilter::make('role')
                //     ->options(Role::class)
                //     ->label('Role'),
                SelectFilter::make('verification_status')
                    ->label('KYC Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'not_submitted' => 'Not Submitted',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! isset($data['value'])) {
                            return $query;
                        }

                        return match ($data['value']) {
                            'pending' => $query->whereHas('verification', fn ($q) => $q->where('status', VerificationStatus::PENDING)),
                            'approved' => $query->whereHas('verification', fn ($q) => $q->where('status', VerificationStatus::APPROVED)),
                            'rejected' => $query->whereHas('verification', fn ($q) => $q->where('status', VerificationStatus::REJECTED)),
                            'not_submitted' => $query->whereDoesntHave('verification'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                // EditAction::make(),
                Action::make('view_verification')
                    ->label('View KYC')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (User $record): ?string => $record->verification 
                        ? route('filament.admin.resources.verifications.edit', ['record' => $record->verification->id])
                        : null)
                    ->visible(fn (User $record): bool => $record->verification !== null),
                Action::make('view_mt_accounts')
                    ->label('MT Accounts')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->url(fn (User $record): string => route('filament.admin.resources.meta-trader-credentials.index', [
                        'tableFilters' => ['user_id' => ['value' => $record->id]]
                    ]))
                    // ->visible(fn (User $record): bool => $record->metaTraderCredentials_count > 0),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
