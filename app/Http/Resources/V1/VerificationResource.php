<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_type' => $this->id_type,
            'id_number' => $this->id_number,
            'id_card_front_img_url' => $this->id_card_front_img_url,
            'id_card_back_img_url' => $this->id_card_back_img_url,
            'selfie_img_url' => $this->selfie_img_url,
            'status' => $this->status,
            'submitted_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
