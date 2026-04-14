<?php

namespace App\Notifications\User;

use App\Models\Signal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSignalNotification extends Notification implements ShouldQueue
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
            ->subject('New Signal: '.$this->signal->symbol.' '.strtoupper($this->signal->action->value).' - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('A new trading signal has been published.')
            ->line('Symbol: '.$this->signal->symbol)
            ->line('Action: '.strtoupper($this->signal->action->value))
            ->line('Entry: '.$this->signal->entry_price)
            ->line('Stop Loss: '.$this->signal->stop_loss)
            ->line('Take Profit 1: '.$this->signal->take_profit_1)
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_signal',
            'title' => 'New Signal: '.$this->signal->symbol.' '.strtoupper($this->signal->action->value),
            'message' => 'A new '.$this->signal->symbol.' '.strtoupper($this->signal->action->value).' signal has been published. Entry: '.$this->signal->entry_price,
            'signal_id' => $this->signal->id,
            'symbol' => $this->signal->symbol,
            'action' => $this->signal->action->value,
            'entry_price' => $this->signal->entry_price,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
