<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ApprovePaymentAction
{
    public function execute(Payment $payment): void
    {
        if ($payment->type === PaymentType::ClassSubscription) {
            throw new \LogicException(
                'Use ApproveSubscriptionAction to approve subscription payments.',
            );
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => PaymentStatus::Approved]);
        });
    }
}
