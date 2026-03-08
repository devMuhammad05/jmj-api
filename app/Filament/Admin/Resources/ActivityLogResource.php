<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verification' => 'warning',
                        'signal' => 'info',
                        'user' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'N/A')
                    ->sortable(),
                TextColumn::make('subject_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('causer.full_name')
                    ->label('User')
                    ->default('System')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('properties')
                    ->label('Changes')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        
                        $changes = [];
                        if (isset($state['attributes'])) {
                            foreach ($state['attributes'] as $key => $value) {
                                $old = $state['old'][$key] ?? 'null';
                                $changes[] = "{$key}: {$old} → {$value}";
                            }
                        }
                        return implode(', ', $changes) ?: 'N/A';
                    })
                    ->limit(50)
                    ->tooltip(function ($state) {
                        if (!$state) return null;
                        
                        $changes = [];
                        if (isset($state['attributes'])) {
                            foreach ($state['attributes'] as $key => $value) {
                                $old = $state['old'][$key] ?? 'null';
                                $changes[] = "{$key}: {$old} → {$value}";
                            }
                        }
                        return implode("\n", $changes);
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log Type')
                    ->options([
                        'verification' => 'Verification',
                        'signal' => 'Signal',
                        'user' => 'User',
                    ]),
                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['value'])) {
                            return $query->where('description', 'like', '%' . $data['value'] . '%');
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
