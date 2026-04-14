<?php

namespace App\Notifications\User;

use App\Models\PoolInvestment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PoolInvestmentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PoolInvestment $investment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pool Investment Rejected - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your investment application for the **'.$this->investment->pool->name.'** pool was not approved.')
            ->when(
                filled($this->investment->rejection_reason),
                fn (MailMessage $mail) => $mail->line('Reason: '.$this->investment->rejection_reason),
            )
            ->line('Please contact support for further assistance.')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pool_investment_rejected',
            'title' => 'Investment Rejected',
            'message' => 'Your investment in the '.$this->investment->pool->name.' pool was not approved.',
            'investment_id' => $this->investment->id,
            'pool_name' => $this->investment->pool->name,
            'reason' => $this->investment->rejection_reason,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
