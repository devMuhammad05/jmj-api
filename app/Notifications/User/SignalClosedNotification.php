<?php

namespace App\Notifications\User;

use App\Models\Signal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignalClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Signal $signal) {}

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
            ->subject('Signal Closed: '.$this->signal->symbol.' - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('The '.$this->signal->symbol.' signal has been closed with status: '.strtoupper($this->signal->status->value).'.')
            ->line('Result: '.$this->signal->pips_result.' pips')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'signal_closed',
            'title' => 'Signal Closed: '.$this->signal->symbol,
            'message' => $this->signal->symbol.' signal closed — '.$this->signal->status->value.' ('.$this->signal->pips_result.' pips).',
            'signal_id' => $this->signal->id,
            'symbol' => $this->signal->symbol,
            'status' => $this->signal->status->value,
            'pips_result' => $this->signal->pips_result,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
