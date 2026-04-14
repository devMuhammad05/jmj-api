<?php

namespace App\Notifications\User;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Subscription Activated - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your subscription to the **'.$this->subscription->plan->name.'** plan has been activated.')
            ->line('Active from: '.$this->subscription->starts_at->toFormattedDateString())
            ->line('Expires on: '.$this->subscription->ends_at->toFormattedDateString())
            ->line('Enjoy full access to all included features.')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_activated',
            'title' => 'Subscription Activated',
            'message' => 'Your '.$this->subscription->plan->name.' plan is now active until '.$this->subscription->ends_at->toFormattedDateString().'.',
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan->name,
            'ends_at' => $this->subscription->ends_at->toISOString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
