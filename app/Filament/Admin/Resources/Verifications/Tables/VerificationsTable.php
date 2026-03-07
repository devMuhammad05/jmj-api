<?php

namespace App\Filament\Admin\Resources\Verifications\Tables;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VerificationsTable
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
                    ->copyable(),
                TextColumn::make('id_type')
                    ->label('ID Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('id_number')
                    ->label('ID Number')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (VerificationStatus $state): string => match ($state) {
                        VerificationStatus::APPROVED => 'success',
                        VerificationStatus::PENDING => 'warning',
                        VerificationStatus::REJECTED => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->limit(30)
                    ->toggleable()
                    ->visible(fn ($record) => $record?->status === VerificationStatus::REJECTED),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(VerificationStatus::class)
                    ->default(VerificationStatus::PENDING->value),
            ])
            ->recordActions([
                Action::make('view_documents')
                    ->label('View Docs')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->modalHeading(fn (Verification $record) => "KYC Documents - {$record->user->full_name}")
                    ->modalContent(fn (Verification $record) => view('filament.admin.verification-documents', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Verification $record) => $record->status === VerificationStatus::PENDING)
                    ->action(function (Verification $record) {
                        $record->update(['status' => VerificationStatus::APPROVED, 'rejection_reason' => null]);
                        Notification::make()
                            ->title('Verification Approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Verification $record) => $record->status === VerificationStatus::PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3)
                            ->placeholder('Explain why this verification is being rejected...'),
                    ])
                    ->action(function (Verification $record, array $data) {
                        $record->update([
                            'status' => VerificationStatus::REJECTED,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Verification Rejected')
                            ->danger()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
