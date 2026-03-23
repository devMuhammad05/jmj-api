<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Verification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Verification $verification) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $userName = $this->verification->user->full_name ?? $this->verification->user->email;
        $reviewUrl = route('filament.admin.resources.verifications.edit', $this->verification);

        return (new MailMessage)
            ->subject('New KYC Verification Submitted')
            ->greeting('Hello Admin,')
            ->line("{$userName} has submitted a KYC verification request and is awaiting review.")
            ->action('Review KYC', $reviewUrl)
            ->line('Please log in to the admin panel to review and take action.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'verification_id' => $this->verification->id,
            'user_id' => $this->verification->user_id,
        ];
    }
}
