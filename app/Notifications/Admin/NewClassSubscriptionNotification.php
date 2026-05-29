<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClassSubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        return ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->payment->user->full_name ?? $this->payment->user->email;
        $reviewUrl = route('filament.admin.resources.payments.edit', $this->payment);

        return (new MailMessage)
            ->subject('New Trading Class Subscription')
            ->greeting('Hello Admin,')
            ->line("{$userName} submitted a payment of \${$this->payment->amount} for the {$this->payment->plan->name} trading class plan.")
            ->action('Review Payment', $reviewUrl)
            ->line('Please log in to the admin panel to review and approve or reject it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_class_subscription',
            'title' => 'New Trading Class Subscription',
            'message' => ($this->payment->user->full_name ?? $this->payment->user->email).' submitted a payment of $'.$this->payment->amount.' for the '.$this->payment->plan->name.' plan.',
            'payment_id' => $this->payment->id,
            'reference' => $this->payment->reference,
            'amount' => $this->payment->amount,
            'user_id' => $this->payment->user_id,
            'plan_name' => $this->payment->plan->name,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
