<?php

namespace App\Jobs;

use App\Models\PersonalizedAnnouncement;
use App\Notifications\User\PersonalizedAnnouncementNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendPersonalizedAnnouncementJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public PersonalizedAnnouncement $announcement) {}

    public function handle(): void
    {
        if ($this->announcement->isSent()) {
            return;
        }

        $users = $this->announcement->users()->get();

        Notification::send($users, new PersonalizedAnnouncementNotification($this->announcement));

        $this->announcement->update(['sent_at' => now()]);
    }
}
