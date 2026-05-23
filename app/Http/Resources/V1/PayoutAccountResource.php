<?php

namespace App\Http\Resources\V1;

use App\Enums\PayoutAccountType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutAccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'label' => $this->label,
            'is_default' => $this->is_default,
            'bank_name' => $this->when($this->type === PayoutAccountType::Bank, $this->bank_name),
            'account_name' => $this->when($this->type === PayoutAccountType::Bank, $this->account_name),
            'account_number' => $this->when($this->type === PayoutAccountType::Bank, $this->account_number),
            'wallet_address' => $this->when($this->type === PayoutAccountType::Crypto, $this->wallet_address),
            'network' => $this->when($this->type === PayoutAccountType::Crypto, $this->network),
            'coin' => $this->when($this->type === PayoutAccountType::Crypto, $this->coin),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
