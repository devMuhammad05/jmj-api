<?php

namespace App\Notifications\User;

use App\Models\ProfitDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfitDistributedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProfitDistribution $distribution) {}

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
            ->subject('Profit Distributed - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your profit distribution for **'.$this->distribution->distribution_date->toFormattedDateString().'** has been processed.')
            ->line('Amount: $'.$this->distribution->profit_amount)
            ->line('Pool Return: '.$this->distribution->pool_return.'%')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'profit_distributed',
            'title' => 'Profit Distributed',
            'message' => 'Your profit of $'.$this->distribution->profit_amount.' has been distributed for '.$this->distribution->distribution_date->toFormattedDateString().'.',
            'distribution_id' => $this->distribution->id,
            'profit_amount' => $this->distribution->profit_amount,
            'pool_return' => $this->distribution->pool_return,
            'distribution_date' => $this->distribution->distribution_date->toDateString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
