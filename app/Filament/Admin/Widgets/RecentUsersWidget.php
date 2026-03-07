<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Role;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentUsersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent User Registrations')
            ->query(
                User::query()
                    ->where('role', Role::User)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('country')
                    ->searchable(),
                TextColumn::make('verification.status')
                    ->label('KYC Status')
                    ->badge()
                    ->default('Not Submitted'),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
