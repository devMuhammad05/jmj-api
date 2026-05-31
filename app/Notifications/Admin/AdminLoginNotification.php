<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $ipAddress,
        public readonly string $userAgent,
        public readonly string $loginAt,
    ) {
        $this->afterCommit();
    }

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
            ->subject('Security Alert: Admin Login Detected')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line('A login to the admin panel was detected on your account.')
            ->line('**Time:** '.$this->loginAt)
            ->line('**IP Address:** '.$this->ipAddress)
            ->line('**Device / Browser:** '.$this->userAgent)
            ->action('Review Admin Panel', route('filament.admin.pages.dashboard'))
            ->line('If this was not you, please change your password immediately and contact support.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Admin Login Detected')
            ->body('A login was detected from IP '.$this->ipAddress.' at '.$this->loginAt.'.')
            ->danger()
            ->getDatabaseMessage();
    }
}
