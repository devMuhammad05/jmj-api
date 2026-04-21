<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\Admin\NewPaymentSubmittedNotification;
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
                'status' => PaymentStatus::Pending,
                'reference' => 'SUB-'.strtoupper((string) Str::ulid()),
                'transaction_id' => 'TXN-'.strtoupper((string) Str::ulid()),
            ]);

            $payment->proofs()->create([
                'payment_proof_url' => $request->string('payment_proof')->value(),
            ]);

            $payment->update(['status' => PaymentStatus::Approved]);

            $payment->load(['plan', 'gateway', 'proofs', 'user']);

            $admins = User::query()->where('role', Role::Admin)->get();
            Notification::send($admins, new NewPaymentSubmittedNotification($payment));

            return $payment;
        });
    }
}
