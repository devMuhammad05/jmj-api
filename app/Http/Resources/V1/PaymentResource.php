<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'plan'           => new PlanResource($this->whenLoaded('plan')),
            'gateway'        => new PaymentGatewayResource($this->whenLoaded('gateway')),
            'amount'         => $this->amount,
            'status'         => $this->status,
            'reference'      => $this->reference,
            'transaction_id' => $this->transaction_id,
            'proofs'         => PaymentProofResource::collection($this->whenLoaded('proofs')),
            'created_at'     => $this->created_at->toDateTimeString(),
        ];
    }
}
