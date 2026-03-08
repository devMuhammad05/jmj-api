<?php

namespace App\Filament\Admin\Resources;

use App\Enums\LogType;
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
                TextColumn::make('properties.log_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state) return 'System';
                        
                        $logType = LogType::tryFrom($state);
                        return $logType ? $logType->label() : ucfirst($state);
                    })
                    ->color(function (?string $state): string {
                        if (!$state) return 'gray';
                        
                        $logType = LogType::tryFrom($state);
                        return $logType ? $logType->color() : 'gray';
                    })
                    ->icon(function (?string $state): ?string {
                        if (!$state) return 'heroicon-o-cog';
                        
                        $logType = LogType::tryFrom($state);
                        return $logType ? $logType->icon() : null;
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'N/A')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('subject_id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('causer.full_name')
                    ->label('User')
                    ->default('System')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('properties')
                    ->label('Details')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        
                        // For MT logs, show specific details
                        if (isset($state['log_type']) && $state['log_type'] === 'mt') {
                            $details = [];
                            if (isset($state['action'])) $details[] = "Action: {$state['action']}";
                            if (isset($state['mt_account_number'])) $details[] = "Account: {$state['mt_account_number']}";
                            if (isset($state['user_email'])) $details[] = "User: {$state['user_email']}";
                            return implode(' | ', $details) ?: 'N/A';
                        }
                        
                        // For other logs, show changes
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
                        
                        return json_encode($state, JSON_PRETTY_PRINT);
                    })
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('log_type')
                    ->label('Log Type')
                    ->options(collect(LogType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
                    ->query(function ($query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            return $query->whereJsonContains('properties->log_type', $data['value']);
                        }
                        return $query;
                    }),
                SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options([
                        'App\Models\User' => 'User',
                        'App\Models\Verification' => 'Verification',
                        'App\Models\Signal' => 'Signal',
                        'App\Models\MetaTraderCredential' => 'MetaTrader Credential',
                    ]),
                SelectFilter::make('causer_id')
                    ->label('User')
                    ->relationship('causer', 'full_name')
                    ->searchable()
                    ->preload(),
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
