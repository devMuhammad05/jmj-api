<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class MetaTraderData
{
    public function __construct(
        public string $mt_account_number,
        public string $mt_password,
        public string $mt_server,
        public float $initial_deposit,
        public string $risk_level,
        public ?string $pool_id = null,
        public ?int $payment_id = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            mt_account_number: $request->string('mt_account_number')->value(),
            mt_password: $request->string('mt_password')->value(),
            mt_server: $request->string('mt_server')->value(),
            initial_deposit: (float) $request->input('initial_deposit'),
            risk_level: $request->string('risk_level')->value(),
            pool_id: $request->string('pool_id')->value() ?: null,
        );
    }
}
