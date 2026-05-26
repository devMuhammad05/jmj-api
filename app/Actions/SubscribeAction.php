<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\Admin\NewPaymentSubmittedNotification;
use App\Services\AdminService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SubscribeAction
{
    public function execute(User $user, SubscribeRequest $request): Payment
    {
        return DB::transaction(function () use ($user, $request) {
            $plan = Plan::findOrFail($request->integer('plan_id'));
            $gateway = PaymentGateway::where('code', $request->string('gateway_code')->value())
                ->where('is_active', true)
                ->firstOrFail();

            $payment = $user->payments()->create([
                'plan_id' => $plan->id,
                'payment_gateway_id' => $gateway->id,
                'amount' => $plan->price,
                'type' => PaymentType::ClassSubscription,
                'status' => PaymentStatus::Pending,
                'reference' => 'SUB-'.strtoupper((string) Str::ulid()),
                'transaction_id' => 'TXN-'.strtoupper((string) Str::ulid()),
            ]);

            $payment->proofs()->create([
                'payment_proof_url' => $request->string('payment_proof')->value(),
            ]);

            $user->subscriptions()->create([
                'plan_id' => $plan->id,
                'payment_id' => $payment->id,
                'starts_at' => null,
                'ends_at' => null,
                'is_active' => false,
            ]);

            $payment->load(['plan', 'gateway', 'proofs', 'user']);

            foreach (app(AdminService::class)->getAdminEmails() as $email) {
                Notification::route('mail', $email)->notify(new NewPaymentSubmittedNotification($payment));
            }

            return $payment;
        });
    }
}
