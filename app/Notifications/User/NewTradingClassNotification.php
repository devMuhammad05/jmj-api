<?php

namespace App\Notifications\User;

use App\Models\TradingClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTradingClassNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TradingClass $tradingClass) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Trading Class: '.$this->tradingClass->title.' - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('A new trading class has been scheduled.')
            ->line('Title: '.$this->tradingClass->title)
            ->line('Date: '.$this->tradingClass->scheduled_at->toFormattedDateString())
            ->line('Time: '.$this->tradingClass->scheduled_at->format('h:i A'))
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_trading_class',
            'title' => 'New Trading Class',
            'message' => '"'.$this->tradingClass->title.'" is scheduled for '.$this->tradingClass->scheduled_at->toFormattedDateString().'.',
            'trading_class_id' => $this->tradingClass->id,
            'class_title' => $this->tradingClass->title,
            'scheduled_at' => $this->tradingClass->scheduled_at->toISOString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
