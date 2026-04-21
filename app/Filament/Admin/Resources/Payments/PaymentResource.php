<?php

namespace App\Filament\Admin\Resources\Payments;

use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Payments\Pages\EditPayment;
use App\Filament\Admin\Resources\Payments\Pages\ListPayments;
use App\Filament\Admin\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Admin\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Payments';

    public static function getNavigationTooltip(): ?string
    {
        return 'Pending payments';
    }

    protected static ?string $modelLabel = 'Payment';

    protected static string|\UnitEnum|null $navigationGroup = 'Subscriptions';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', [
            PaymentStatus::Pending,
        ])->count() ?: null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending payments';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
