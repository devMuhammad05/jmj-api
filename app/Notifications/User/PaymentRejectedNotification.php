<?php

namespace App\Notifications\User;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Payment Rejected - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Your payment (Ref: '.$this->payment->reference.') for the **'.$this->payment->plan->name.'** plan has been rejected.')
            ->line('Please contact support or re-submit your payment proof.')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_rejected',
            'title' => 'Payment Rejected',
            'message' => 'Your payment for the '.$this->payment->plan->name.' plan was rejected. Please re-submit.',
            'payment_id' => $this->payment->id,
            'reference' => $this->payment->reference,
            'plan_name' => $this->payment->plan->name,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
