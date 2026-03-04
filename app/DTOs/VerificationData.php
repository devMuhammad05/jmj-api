<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\IdType;
use Illuminate\Http\Request;

readonly class VerificationData
{
    public function __construct(
        public IdType $id_type,
        public string $id_number,
        public string $id_card_front_img_url,
        public ?string $id_card_back_img_url,
        public string $selfie_img_url,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_type: IdType::from($request->string('id_type')->value()),
            id_number: $request->string('id_number')->value(),
            id_card_front_img_url: $request->string('id_card_front_img_url')->value(),
            id_card_back_img_url: $request->string('id_card_back_img_url')->value(),
            selfie_img_url: $request->string('selfie_img_url')->value(),
        );
    }
}
