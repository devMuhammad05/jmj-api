<?php

namespace App\Notifications\User;

use App\Models\PoolInvestment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PoolInvestmentApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Pool Investment Approved - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your investment in the **'.$this->investment->pool->name.'** pool has been approved.')
            ->line('Contribution: $'.$this->investment->contribution)
            ->line('Share: '.$this->investment->share_percentage.'%')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pool_investment_approved',
            'title' => 'Investment Approved',
            'message' => 'Your investment in the '.$this->investment->pool->name.' pool has been approved.',
            'investment_id' => $this->investment->id,
            'pool_name' => $this->investment->pool->name,
            'contribution' => $this->investment->contribution,
            'share_percentage' => $this->investment->share_percentage,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
