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
        public ?string $mt_account_number = null,
        public ?string $mt_password = null,
        public ?string $mt_server = null,
        public ?string $platform_type = null,
        public ?float $initial_deposit = null,
        public ?string $risk_level = null,
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
            mt_account_number: $request->filled('mt_account_number') ? $request->string('mt_account_number')->value() : null,
            mt_password: $request->filled('mt_password') ? $request->string('mt_password')->value() : null,
            mt_server: $request->filled('mt_server') ? $request->string('mt_server')->value() : null,
            platform_type: $request->filled('platform_type') ? $request->string('platform_type')->value() : null,
            initial_deposit: $request->filled('initial_deposit') ? $request->float('initial_deposit') : null,
            risk_level: $request->filled('risk_level') ? $request->string('risk_level')->value() : null,
        );
    }
}
