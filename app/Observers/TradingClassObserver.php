<?php

namespace App\Observers;

use App\Models\TradingClass;
use App\Models\User;
use App\Notifications\User\NewTradingClassNotification;
use Illuminate\Support\Facades\Notification;

class TradingClassObserver
{
    /**
     * Handle the TradingClass "updated" event.
     */
    public function updated(TradingClass $tradingClass): void
    {
        if (! $tradingClass->wasChanged('is_published') || ! $tradingClass->is_published) {
            return;
        }

        $planIds = $tradingClass->plans()->pluck('plans.id');

        if ($planIds->isEmpty()) {
            // Free class — notify all users
            $users = User::query()->select('id')->get();
        } else {
            $users = User::query()
                ->select('users.id')
                ->join('subscriptions', 'subscriptions.user_id', '=', 'users.id')
                ->where('subscriptions.is_active', true)
                ->where('subscriptions.ends_at', '>', now())
                ->whereIn('subscriptions.plan_id', $planIds)
                ->get();
        }

        Notification::send($users, new NewTradingClassNotification($tradingClass));
    }
}
