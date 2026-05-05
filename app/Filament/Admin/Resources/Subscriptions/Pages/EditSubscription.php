<?php

namespace App\Filament\Admin\Resources\Subscriptions\Pages;

use App\Actions\ApproveSubscriptionAction;
use App\Actions\RejectSubscriptionAction;
use App\Enums\SubscriptionStatus;
use App\Filament\Admin\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Subscription')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Subscription')
                ->modalDescription('This will activate the subscription and approve the associated payment. Are you sure?')
                ->visible(fn (): bool => $this->record->status === SubscriptionStatus::Pending)
                ->action(function (): void {
                    try {
                        app(ApproveSubscriptionAction::class)->execute($this->record);

                        Notification::make()
                            ->title('Subscription approved')
                            ->body('The subscription has been activated.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['is_active', 'starts_at', 'ends_at']);
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Approval failed')
                            ->body('An error occurred. Please try again.')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('reject')
                ->label('Reject Subscription')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Subscription')
                ->modalDescription('This will reject the subscription and the associated payment. Are you sure?')
                ->visible(fn (): bool => $this->record->status === SubscriptionStatus::Pending)
                ->action(function (): void {
                    try {
                        app(RejectSubscriptionAction::class)->execute($this->record);

                        Notification::make()
                            ->title('Subscription rejected')
                            ->success()
                            ->send();

                        $this->refreshFormData(['is_active', 'starts_at', 'ends_at']);
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Rejection failed')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('view_payment')
                ->label('View Payment')
                ->icon('heroicon-o-credit-card')
                ->color('info')
                ->url(fn (): ?string => $this->record->payment_id !== null
                    ? route('filament.admin.resources.payments.index').'?tableSearch='.$this->record->payment?->reference
                    : null
                )
                ->visible(fn (): bool => $this->record->payment_id !== null)
                ->openUrlInNewTab(),

            DeleteAction::make(),
        ];
    }
}
