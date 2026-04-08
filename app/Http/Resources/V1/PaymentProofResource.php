<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentProofResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'url'        => asset('storage/' . $this->payment_proof_url),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
