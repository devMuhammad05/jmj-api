<?php

namespace App\Filament\Admin\Resources\PersonalizedAnnouncements\Tables;

use App\Jobs\SendPersonalizedAnnouncementJob;
use App\Models\PersonalizedAnnouncement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PersonalizedAnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('users_count')
                    ->label('Recipients')
                    ->counts('users')
                    ->badge()
                    ->color('primary')
                    ->suffix(' users'),

                TextColumn::make('sent_at')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn ($record): string => $record->sent_at ? 'Sent' : 'Draft')
                    ->color(fn (string $state): string => $state === 'Sent' ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send Personalized Announcement')
                    ->modalDescription('This will immediately notify the selected recipients. This action cannot be undone.')
                    ->disabled(fn (PersonalizedAnnouncement $record): bool => $record->isSent())
                    ->action(function (PersonalizedAnnouncement $record): void {
                        SendPersonalizedAnnouncementJob::dispatch($record);

                        Notification::make()
                            ->title('Announcement queued')
                            ->body('The announcement is being sent to the selected recipients.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
