<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ClientData
{
    public function __construct(
        public string $full_name,
        public string $email,
        public string $phone,
        public MetaTraderData $metaTrader,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            full_name: $request->string('full_name')->value(),
            email: $request->string('email')->value(),
            phone: $request->string('phone')->value(),
            metaTrader: MetaTraderData::fromRequest($request),
        );
    }
}
