<?php

namespace App\Actions;

use App\Enums\PlanType;
use App\Enums\SignalStatus;
use App\Models\Signal;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GetSignalsAction
{
    /**
     * Execute the action to get filtered and paginated signals.
     */
    public function execute(Request $request, bool $activeOnly = false): LengthAwarePaginator
    {
        /** @var User|null $user */
        $user = $request->user();
        $subscription = $user?->activeSubscriptionFor(PlanType::Signals);
        $userPlan = $subscription?->plan;

        $query = Signal::query()
            ->where('is_published', true)
            ->where(function (Builder $q) use ($userPlan): void {
                $q->where('is_free', true);

                if ($userPlan) {
                    $q->orWhereHas(
                        'plans',
                        fn (Builder $pq) => $pq->where('plans.type', $userPlan->type)
                            ->where('plans.level', '<=', $userPlan->level),
                    );
                }
            });

        // Apply filters
        $this->applyFilters($query, $request, $activeOnly);

        // Sort by latest first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $perPage = $request->input('per_page', 15);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters(Builder $query, Request $request, bool $activeOnly): void
    {
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by symbol
        if ($request->has('symbol')) {
            $query->where('symbol', 'like', '%'.$request->symbol.'%');
        }

        // Filter by action (buy/sell)
        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        // Get only active signals by default (unless status is specified or include_all is set)
        if ($activeOnly || (! $request->has('status') && ! $request->has('include_all'))) {
            $query->where('status', SignalStatus::ACTIVE);
        }
    }
}
