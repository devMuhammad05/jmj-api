<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\SubscribeAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Http\Resources\V1\SubscriptionResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends ApiController
{
    public function __construct(private readonly SubscribeAction $subscribeAction) {}

    /**
     * POST /subscribe
     * Initiates a subscription request
     */
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $plan = Plan::findOrFail($request->integer('plan_id'));
        $user = $request->user();

        if ($user->activeSubscriptionFor($plan->type) !== null) {
            return $this->errorResponse(
                "You already have an active {$plan->type->label()} subscription.",
                \Symfony\Component\HttpFoundation\Response::HTTP_CONFLICT,
            );
        }

        $payment = $this->subscribeAction->execute($user, $request);

        return $this->createdResponse(
            'Subscription request submitted. Awaiting admin approval.',
            new PaymentResource($payment),
        );
    }

    /**
     * GET /subscriptions/current
     * Returns the authenticated user's active subscription.
     */
    public function current(Request $request): JsonResponse
    {
        $subscription = $request->user()
            ->subscriptions()
            ->with('plan')
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->latest('starts_at')
            ->first();

        if (! $subscription) {
            return $this->notFoundResponse('You do not have an active subscription.');
        }

        return $this->successResponse(
            'Active subscription retrieved successfully',
            new SubscriptionResource($subscription),
        );
    }

    /**
     * GET /subscriptions
     * Returns a paginated list of the authenticated user's subscriptions.
     */
    public function index(Request $request): JsonResponse
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('plan')
            ->orderBy('starts_at', 'desc')
            ->paginate();

        return $this->successResponse(
            'Subscriptions retrieved successfully',
            SubscriptionResource::collection($subscriptions),
        );
    }
}
