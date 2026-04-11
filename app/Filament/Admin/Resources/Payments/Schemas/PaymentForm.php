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

                    TextInput::make('plan_name')
                        ->label('Plan')
                        ->disabled()
                        ->dehydrated(false)
                        ->afterStateHydrated(fn ($component, $record) => $component->state($record?->plan?->name)),

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

            Section::make('Admin Review')
                ->description('Update the payment status.')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Select::make('status')
                        ->options(PaymentStatus::class)
                        ->required()
                        ->native(false),
                ])
                ->columns(1)
                ->columnSpan(1),

            Section::make('Payment Proofs')
                ->description('Proof of payment submitted by the user.')
                ->icon('heroicon-o-photo')
                ->schema([
                    Placeholder::make('proof_urls')
                        ->label('')
                        ->content(fn ($record): HtmlString|string => $record?->proofs->isNotEmpty()
                            ? new HtmlString(
                                $record->proofs->map(fn ($proof): string => sprintf(
                                    '<a href="%s" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:underline break-all">%s</a>',
                                    e($proof->payment_proof_url),
                                    e($proof->payment_proof_url),
                                ))->join('<br><br>')
                            )
                            : 'No proofs submitted.'
                        )
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
