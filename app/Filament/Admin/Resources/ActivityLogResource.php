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
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->getStateUsing(fn ($record) => self::resolveLogType($record))
                    ->formatStateUsing(fn (?string $state): string => LogType::tryFrom($state)?->getLabel() ?? ucfirst($state ?? 'System'))
                    ->color(fn (?string $state): string => LogType::tryFrom($state)?->getColor() ?? 'gray')
                    ->icon(fn (?string $state): ?string => LogType::tryFrom($state)?->getIcon() ?? 'heroicon-o-cog')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('properties->log_type', $direction))
                    ->searchable(query: fn ($query, $search) => $query->where('properties->log_type', 'like', "%{$search}%")),
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
                    ->formatStateUsing(fn ($state) => self::formatActivityDetails($state))
                    ->limit(50)
                    ->tooltip(function ($state) {
                        if (! $state) {
                            return null;
                        }

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
                    ->options(LogType::class)
                    ->query(function ($query, array $data) {
                        if (isset($data['value']) && $data['value']) {
                            return $query->whereJsonContains('properties->log_type', $data['value']);
                        }

                        return $query;
                    }),
                SelectFilter::make('subject_type')
                    ->label('Model')
                    ->options(\App\Enums\ActivitySubjectType::class),
                SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(fn () => \App\Models\User::query()->orderBy('full_name')->pluck('full_name', 'id')->toArray())
                    ->searchable(),
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

    protected static function formatActivityDetails($state): string
    {
        if (! $state) {
            return 'N/A';
        }

        // Convert to array if it's a Collection or object
        $data = is_array($state) ? $state : (method_exists($state, 'toArray') ? $state->toArray() : (array) $state);

        // If it has log_type OR specific MT keys, format as MT log
        if ((isset($data['log_type']) && $data['log_type'] === 'mt') || isset($data['mt_account_number']) || isset($data['action'])) {
            $details = [];
            foreach (['action', 'mt_account_number', 'user_email', 'accessed_at'] as $key) {
                if (isset($data[$key])) {
                    $label = str_replace('_', ' ', ucfirst($key));
                    $details[] = "{$label}: {$data[$key]}";
                }
            }

            if ($details) {
                return implode(' | ', $details);
            }
        }

        // For logs with attributes/old (Spatie standard)
        $changes = [];
        if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $old = isset($data['old'][$key]) ? (is_array($data['old'][$key]) ? json_encode($data['old'][$key]) : $data['old'][$key]) : 'null';
                $changes[] = "{$key}: {$old} → {$value}";
            }
        }

        if ($changes) {
            return implode(', ', $changes);
        }

        // Fallback: just show keys and values that aren't metadata
        $fallback = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['log_type', 'attributes', 'old'])) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $fallback[] = "{$key}: {$value}";
        }

        return implode(', ', $fallback) ?: 'N/A';
    }

    protected static function resolveLogType($record): string
    {
        $props = $record->properties;
        $data = is_array($props) ? $props : (method_exists($props, 'toArray') ? $props->toArray() : (array) $props);

        if (isset($data['log_type'])) {
            return $data['log_type'];
        }

        if (isset($data['mt_account_number']) || isset($data['action'])) {
            return 'mt';
        }

        return 'system';
    }
}
