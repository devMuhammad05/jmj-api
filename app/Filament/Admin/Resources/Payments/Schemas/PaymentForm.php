<?php

namespace App\Filament\Admin\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Payment Details')
                ->description('Details of the payment submitted by the user.')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    TextInput::make('user_full_name')
                        ->label('User')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->user?->full_name)),

                    TextInput::make('type')
                        ->label('Payment Type')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->type ? format_status_text($record->type) : null)),

                    TextInput::make('gateway_name')
                        ->label('Gateway')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->gateway?->name)),

                    TextInput::make('amount')
                        ->label('Amount ($)')
                        ->prefix('$')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('reference')
                        ->label('Reference')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpan(1),

            Section::make('Payment Proof')
                ->description('Proof of payment submitted by the user.')
                ->icon('heroicon-o-photo')
                ->schema([
                    Placeholder::make('payment proof')
                        ->label('')
                        ->content(fn ($record): HtmlString|string => $record?->proofs->isNotEmpty()
                            ? new HtmlString(
                                $record->proofs->map(fn ($proof): string => sprintf(
                                    '<a href="%s" target="_blank" rel="noopener noreferrer"><img src="%s" alt="Payment Proof" style="max-width: 400px; max-height: 700px; margin-bottom: 8px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: block;"></a>',
                                    e($proof->payment_proof_url),
                                    e($proof->payment_proof_url),
                                ))->join('<br>')
                            )
                            : 'No proofs submitted.'
                        )
                        ->columnSpanFull(),
                ])
                ->columnSpan(1),

            Section::make('Admin Review')
                ->description('Update the payment status.')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Select::make('status')
                        ->options(PaymentStatus::class)
                        ->required()
                        ->native(false),
                ])
                ->columnSpanFull()
        ]);
    }
}
