<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\PoolInvestment;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPoolInvestmentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PoolInvestment $investment)
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
        $userName = $this->investment->user->full_name ?? $this->investment->user->email;
        $reviewUrl = route('filament.admin.resources.pool-investments.edit', $this->investment);

        return (new MailMessage)
            ->subject('New Pool Investment Submitted')
            ->greeting('Hello Admin,')
            ->line("{$userName} submitted a pool investment of \${$this->investment->amount_paid} for the {$this->investment->pool->name} pool.")
            ->action('Review Investment', $reviewUrl)
            ->line('Please log in to the admin panel to review and approve or reject it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $userName = $this->investment->user->full_name ?? $this->investment->user->email;

        return FilamentNotification::make()
            ->title('New Pool Investment Submitted')
            ->body($userName.' submitted a pool investment of $'.$this->investment->amount_paid.' for the '.$this->investment->pool->name.' pool.')
            ->warning()
            ->getDatabaseMessage();
    }
}
