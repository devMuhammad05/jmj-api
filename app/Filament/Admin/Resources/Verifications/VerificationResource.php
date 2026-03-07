<?php

namespace App\Filament\Admin\Resources\Verifications;

use App\Filament\Admin\Resources\Verifications\Pages\CreateVerification;
use App\Filament\Admin\Resources\Verifications\Pages\EditVerification;
use App\Filament\Admin\Resources\Verifications\Pages\ListVerifications;
use App\Filament\Admin\Resources\Verifications\Schemas\VerificationForm;
use App\Filament\Admin\Resources\Verifications\Tables\VerificationsTable;
use App\Models\Verification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VerificationResource extends Resource
{
    protected static ?string $model = Verification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static ?string $navigationLabel = 'KYC Verifications';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', \App\Enums\VerificationStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return VerificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VerificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVerifications::route('/'),
            'create' => CreateVerification::route('/create'),
            'edit' => EditVerification::route('/{record}/edit'),
        ];
    }
}
