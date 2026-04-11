<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class InitiatePaymentData
{
    public function __construct(
        public int $plan_id,
        public string $gateway_code,
        public float $amount,
    ) {}

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            plan_id: $request->integer('plan_id'),
            gateway_code: $request->string('gateway_code')->value(),
            amount: $request->float('amount'),
        );
    }
}
