<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PlanType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    public function handle(Request $request, Closure $next, ?string $type = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'An active subscription is required to access this resource.',
            ], 403);
        }

        if ($type !== null) {
            $planType = PlanType::tryFrom($type);

            if (! $planType || ! $user->activeSubscriptionFor($planType)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An active subscription is required to access this resource.',
                ], 403);
            }
        } elseif (! $user->activeSubscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'An active subscription is required to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
