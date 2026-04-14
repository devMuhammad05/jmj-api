<?php

namespace App\Notifications\Admin;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPaymentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->payment->user->full_name ?? $this->payment->user->email;
        $reviewUrl = route('filament.admin.resources.payments.edit', $this->payment);

        return (new MailMessage)
            ->subject('New Payment Submitted')
            ->greeting('Hello Admin,')
            ->line("{$userName} submitted a payment of \${$this->payment->amount} for the {$this->payment->plan->name} plan.")
            ->action('Review Payment', $reviewUrl)
            ->line('Please log in to the admin panel to review and approve or reject it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_payment_submitted',
            'title' => 'New Payment Submitted',
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
