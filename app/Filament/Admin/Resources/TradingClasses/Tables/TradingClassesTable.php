<?php

namespace App\Filament\Admin\Resources\TradingClasses\Tables;

use App\Enums\ClassPlatform;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TradingClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Class Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y h:i A')
                    ->sortable(),

                TextColumn::make('platform')
                    ->badge()
                    ->color(
                        fn (ClassPlatform $state): string => match ($state) {
                            ClassPlatform::ZOOM => 'info',
                            ClassPlatform::TELEGRAM => 'primary',
                            ClassPlatform::GOOGLE_MEET => 'success',
                            ClassPlatform::YOUTUBE => 'danger',
                        },
                    )
                    ->sortable(),

                TextColumn::make('meeting_link')
                    ->label('Link')
                    ->limit(30)
                    ->url(fn ($record) => $record->meeting_link)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('platform')->options(ClassPlatform::class),

                SelectFilter::make('is_published')
                    ->label('Visibility')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Hidden',
                    ]),
            ])
            ->recordActions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('scheduled_at', 'desc');
    }
}
