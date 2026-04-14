<?php

namespace App\Notifications\User;

use App\Models\TradingClass;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TradingClassReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TradingClass $tradingClass) {}

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
            ->subject('Class Starting in 1 Hour: '.$this->tradingClass->title.' - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('"'.$this->tradingClass->title.'" starts in 1 hour.')
            ->line('Time: '.$this->tradingClass->scheduled_at->format('h:i A'))
            ->when(
                filled($this->tradingClass->meeting_link),
                fn (MailMessage $mail) => $mail->action('Join Class', $this->tradingClass->meeting_link),
            )
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trading_class_reminder',
            'title' => 'Class Starting Soon',
            'message' => '"'.$this->tradingClass->title.'" starts in 1 hour. Get ready!',
            'trading_class_id' => $this->tradingClass->id,
            'class_title' => $this->tradingClass->title,
            'scheduled_at' => $this->tradingClass->scheduled_at->toISOString(),
            'meeting_link' => $this->tradingClass->meeting_link,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
