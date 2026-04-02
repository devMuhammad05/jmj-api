<?php

namespace App\Http\Resources\V1;

use App\Enums\PoolInvestmentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoolInvestmentResource extends JsonResource
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
            'pool' => new PoolResource($this->whenLoaded('pool')),
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'contribution' => $this->contribution,
            'share_percentage' => $this->share_percentage,
            'status' => $this->status,
            'terms_accepted' => $this->terms_accepted,
            'verified_at' => $this->verified_at?->toDateTimeString(),
            'rejection_reason' => $this->when($this->status->value === 'rejected', $this->rejection_reason),
            'pool_meta_trader_account' => $this->when(
                $this->status === PoolInvestmentStatus::VERIFIED
                    && $this->relationLoaded('pool')
                    && $this->pool->relationLoaded('metaTraderCredential')
                    && $this->pool->metaTraderCredential !== null,
                fn () => [
                    'mt_account_number' => $this->pool->metaTraderCredential->mt_account_number,
                    'mt_server' => $this->pool->metaTraderCredential->mt_server,
                    'platform_type' => $this->pool->metaTraderCredential->platform_type,
                    'risk_level' => $this->pool->metaTraderCredential->risk_level,
                    'initial_deposit' => $this->pool->metaTraderCredential->initial_deposit,
                ]
            ),
            'submitted_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
