<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class PoolInvestmentData
{
    public function __construct(
        public string $pool_id,
        public string $full_name,
        public string $phone_number,
        public string $bank_name,
        public string $account_number,
        public string $account_name,
        public float $contribution,
        public string $payment_proof_path,
        public bool $terms_accepted,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            pool_id: $request->string('pool_id')->value(),
            full_name: $request->string('full_name')->value(),
            phone_number: $request->string('phone_number')->value(),
            bank_name: $request->string('bank_name')->value(),
            account_number: $request->string('account_number')->value(),
            account_name: $request->string('account_name')->value(),
            contribution: $request->float('contribution'),
            payment_proof_path: $request->string('payment_proof_path')->value(),
            terms_accepted: $request->boolean('terms_accepted'),
        );
    }
}
