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
        public string $id_card_front_img,
        public ?string $id_card_back_img,
        public string $selfie_img,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_type: IdType::from($request->string('id_type')->value()),
            id_number: $request->string('id_number')->value(),
            id_card_front_img: $request->string('id_card_front_img')->value(),
            id_card_back_img: $request->string('id_card_back_img')->value(),
            selfie_img: $request->string('selfie_img')->value(),
        );
    }
}
