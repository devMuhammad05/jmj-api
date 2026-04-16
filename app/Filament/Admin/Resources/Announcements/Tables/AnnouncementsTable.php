<?php

namespace App\Filament\Admin\Resources\Announcements\Tables;

use App\Enums\AnnouncementTarget;
use App\Jobs\SendAnnouncementJob;
use App\Models\Announcement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('target_audience')
                    ->badge()
                    ->color(
                        fn (AnnouncementTarget $state): string => match ($state) {
                            AnnouncementTarget::All => 'info',
                            AnnouncementTarget::Subscribers => 'success',
                            AnnouncementTarget::Plan => 'warning',
                        }
                    )
                    ->sortable(),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color('primary')
                    ->placeholder('—'),

                TextColumn::make('sent_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Sent' : 'Draft')
                    ->color(fn ($state): string => $state ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('target_audience')
                    ->options(AnnouncementTarget::class),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send Announcement')
                    ->modalDescription('This will immediately notify the target audience. This action cannot be undone.')
                    ->disabled(fn (Announcement $record): bool => $record->isSent())
                    ->action(function (Announcement $record): void {
                        SendAnnouncementJob::dispatch($record);

                        Notification::make()
                            ->title('Announcement queued')
                            ->body('The announcement is being sent to the target audience.')
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
