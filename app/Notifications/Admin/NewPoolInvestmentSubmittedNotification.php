<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\PoolInvestment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPoolInvestmentSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PoolInvestment $investment) {}

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
        $userName = $this->investment->user->full_name ?? $this->investment->user->email;
        $reviewUrl = route('filament.admin.resources.pool-investments.edit', $this->investment);

        return (new MailMessage)
            ->subject('New Pool Investment Submitted')
            ->greeting('Hello Admin,')
            ->line("{$userName} submitted a pool investment of \${$this->investment->amount_paid} for the {$this->investment->pool->name} pool.")
            ->action('Review Investment', $reviewUrl)
            ->line('Please log in to the admin panel to review and approve or reject it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_pool_investment_submitted',
            'title' => 'New Pool Investment Submitted',
            'message' => ($this->investment->user->full_name ?? $this->investment->user->email).' submitted a pool investment of $'.$this->investment->amount_paid.' for the '.$this->investment->pool->name.' pool.',
            'investment_id' => $this->investment->id,
            'pool_id' => $this->investment->pool_id,
            'pool_name' => $this->investment->pool->name,
            'amount_paid' => $this->investment->amount_paid,
            'user_id' => $this->investment->user_id,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
