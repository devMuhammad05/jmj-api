<?php

namespace App\Filament\Admin\Resources\Payments\Pages;

use App\Actions\ApprovePaymentAction;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Payment')
                ->modalDescription('This will approve the payment and create an active subscription for the user. Are you sure?')
                ->visible(fn (): bool => in_array($this->record->status, [
                    PaymentStatus::Submitted,
                    PaymentStatus::UnderReview,
                ]))
                ->action(function (): void {
                    try {
                        app(ApprovePaymentAction::class)->execute($this->record);

                        Notification::make()
                            ->title('Payment approved')
                            ->body('The payment has been approved and a subscription has been created.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['status']);
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Approval failed')
                            ->body('An error occurred while approving the payment. Please try again.')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('reject')
                ->label('Reject Payment')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Payment')
                ->modalDescription('This will reject the payment. No subscription will be created. Are you sure?')
                ->visible(fn (): bool => in_array($this->record->status, [
                    PaymentStatus::Pending,
                    PaymentStatus::Submitted,
                    PaymentStatus::UnderReview,
                ]))
                ->action(function (): void {
                    try {
                        $this->record->update(['status' => PaymentStatus::Rejected]);

                        Notification::make()
                            ->title('Payment rejected')
                            ->body('The payment has been rejected.')
                            ->success()
                            ->send();

                        $this->refreshFormData(['status']);
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Rejection failed')
                            ->body('An error occurred while rejecting the payment. Please try again.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
