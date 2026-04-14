<?php

namespace App\Notifications\User;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Verification $verification) {}

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
            ->subject('KYC Verification Rejected - '.config('app.name'))
            ->greeting('Hello '.$notifiable->full_name.'!')
            ->line('Unfortunately, your KYC verification was not approved.')
            ->when(
                filled($this->verification->rejection_reason),
                fn (MailMessage $mail) => $mail->line('Reason: '.$this->verification->rejection_reason),
            )
            ->line('You may re-submit your documents for another review.')
            ->salutation('Best regards, The '.config('app.name').' Team');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'kyc_rejected',
            'title' => 'KYC Verification Rejected',
            'message' => 'Your identity verification was rejected. Please re-submit your documents.',
            'reason' => $this->verification->rejection_reason,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
