<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentGatewayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'wallet_address' => $this->wallet_address,
            'network' => $this->network,
            'bar_code_url' => $this->bar_code_path
                ? Storage::disk('public')->url($this->bar_code_path)
                : null,
            'config' => $this->config,
        ];
    }
}
