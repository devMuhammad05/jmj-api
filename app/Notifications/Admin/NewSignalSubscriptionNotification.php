<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\Payment;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSignalSubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
        $this->afterCommit();
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->payment->user->full_name ?? $this->payment->user->email;
        $reviewUrl = route('filament.admin.resources.subscriptions.index');

        return (new MailMessage)
            ->subject('New Signal Subscription')
            ->greeting('Hello Admin,')
            ->line("{$userName} submitted a payment of \${$this->payment->amount} for the {$this->payment->plan->name} signals plan.")
            ->action('Review Payment', $reviewUrl)
            ->line('Please log in to the admin panel to review and approve or reject it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $userName = $this->payment->user->full_name ?? $this->payment->user->email;

        return FilamentNotification::make()
            ->title('New Signal Subscription')
            ->body($userName.' submitted a payment of $'.$this->payment->amount.' for the '.$this->payment->plan->name.' plan.')
            ->warning()
            ->getDatabaseMessage();
    }
}
