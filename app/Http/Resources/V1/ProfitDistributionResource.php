<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfitDistributionResource extends JsonResource
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
            'distribution_date' => $this->distribution_date->toDateString(),
            'profit_amount' => $this->profit_amount,
            'pool_return' => $this->pool_return,
            'status' => $this->status,
            'processed_at' => $this->processed_at?->toDateTimeString(),
            'failure_reason' => $this->when($this->status->value === 'failed', $this->failure_reason),
        ];
    }
}
