<?php

namespace App\Http\Resources\V1;

use App\Enums\PoolInvestmentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoolResource extends JsonResource
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
            'name' => $this->name,
            'total_amount' => $this->total_amount,
            'total_number_of_investors' => $this->number_of_investors,
            'each_contribution_amount' => $this->each_contribution_amount,
            'approved_investors_count' => $this->approved_investors_count ?? 0,
            'status' => $this->status,
            'user_investment' => $this->whenLoaded('poolInvestments', function () {
                $investment = $this->poolInvestments->first();

                if (! $investment) {
                    return null;
                }

                return [
                    'id' => $investment->id,
                    'status' => $investment->status,
                    'is_approved' => $investment->status === PoolInvestmentStatus::VERIFIED,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
