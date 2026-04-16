<?php

namespace App\Jobs;

use App\Enums\AnnouncementTarget;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\User\AnnouncementNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendAnnouncementJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Announcement $announcement) {}

    public function handle(): void
    {
        if ($this->announcement->isSent()) {
            return;
        }

        $users = $this->resolveRecipients();

        Notification::send($users, new AnnouncementNotification($this->announcement));

        $this->announcement->update(['sent_at' => now()]);
    }

    private function resolveRecipients(): Collection
    {
        return match ($this->announcement->target_audience) {
            AnnouncementTarget::All => User::query()->get(),

            AnnouncementTarget::Subscribers => User::query()
                ->join('subscriptions', 'subscriptions.user_id', '=', 'users.id')
                ->where('subscriptions.is_active', true)
                ->where('subscriptions.ends_at', '>', now())
                ->select('users.*')
                ->distinct()
                ->get(),

            AnnouncementTarget::Plan => User::query()
                ->join('subscriptions', 'subscriptions.user_id', '=', 'users.id')
                ->where('subscriptions.is_active', true)
                ->where('subscriptions.ends_at', '>', now())
                ->where('subscriptions.plan_id', $this->announcement->plan_id)
                ->select('users.*')
                ->distinct()
                ->get(),
        };
    }
}
