<?php

namespace App\Observers;

use App\Enums\SignalStatus;
use App\Models\Signal;
use App\Models\User;
use App\Notifications\User\NewSignalNotification;
use App\Notifications\User\SignalClosedNotification;
use Illuminate\Support\Facades\Notification;

class SignalObserver
{
    /**
     * Handle the Signal "updated" event.
     */
    public function updated(Signal $signal): void
    {
        // Notify eligible subscribers when a signal is published
        if ($signal->wasChanged('is_published') && $signal->is_published) {
            $this->notifyEligibleUsers($signal, new NewSignalNotification($signal));
        }

        // Notify when a signal is closed (TP, SL, Closed, Cancelled)
        if ($signal->wasChanged('status') && in_array($signal->status, [
            SignalStatus::TP,
            SignalStatus::SL,
            SignalStatus::CLOSED,
            SignalStatus::CANCELLED,
        ])) {
            $this->notifyEligibleUsers($signal, new SignalClosedNotification($signal));
        }
    }

    /**
     * Notify all users who have access to this signal.
     */
    private function notifyEligibleUsers(Signal $signal, object $notification): void
    {
        if ($signal->is_free) {
            // All registered users
            $users = User::query()->select('id')->get();
            Notification::send($users, $notification);

            return;
        }

        // Only users subscribed to a plan that includes this signal
        $planIds = $signal->plans()->pluck('plans.id');

        $users = User::query()
            ->select('users.id')
            ->join('subscriptions', 'subscriptions.user_id', '=', 'users.id')
            ->where('subscriptions.is_active', true)
            ->where('subscriptions.ends_at', '>', now())
            ->whereIn('subscriptions.plan_id', $planIds)
            ->get();

        Notification::send($users, $notification);
    }
}
