<?php

namespace App\Notifications\User;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = (int) now()->diffInDays($this->subscription->ends_at);

        return (new MailMessage)
            ->subject('Subscription Expiring Soon - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your **'.$this->subscription->plan->name.'** subscription expires in '.$daysLeft.' day(s) on '.$this->subscription->ends_at->toFormattedDateString().'.')
            ->line('Renew your subscription to keep uninterrupted access.')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysLeft = (int) now()->diffInDays($this->subscription->ends_at);

        return [
            'type' => 'subscription_expiring',
            'title' => 'Subscription Expiring Soon',
            'message' => 'Your '.$this->subscription->plan->name.' plan expires in '.$daysLeft.' day(s). Renew to stay connected.',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan->name,
            'ends_at' => $this->subscription->ends_at->toISOString(),
            'days_left' => $daysLeft,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
