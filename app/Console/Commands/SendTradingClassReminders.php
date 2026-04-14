<?php

namespace App\Console\Commands;

use App\Models\TradingClass;
use App\Models\User;
use App\Notifications\User\TradingClassReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendTradingClassReminders extends Command
{
    protected $signature = 'notifications:trading-class-reminders';

    protected $description = 'Notify users about trading classes starting in 1 hour.';

    public function handle(): int
    {
        $classes = TradingClass::query()
            ->with('plans')
            ->where('is_published', true)
            ->whereBetween('scheduled_at', [now()->addMinutes(55), now()->addMinutes(65)])
            ->get();

        foreach ($classes as $tradingClass) {
            $planIds = $tradingClass->plans()->pluck('plans.id');

            if ($planIds->isEmpty()) {
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

            Notification::send($users, new TradingClassReminderNotification($tradingClass));
        }

        $this->info("Sent reminders for {$classes->count()} upcoming class(es).");

        return self::SUCCESS;
    }
}
