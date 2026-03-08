<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignalResource extends JsonResource
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
            'symbol' => $this->symbol,
            'action' => $this->action->value,
            'type' => $this->type->value,
            'entry_price' => $this->entry_price,
            'stop_loss' => $this->stop_loss,
            'take_profit_1' => $this->take_profit_1,
            'take_profit_2' => $this->take_profit_2,
            'take_profit_3' => $this->take_profit_3,
            'status' => $this->status->value,
            'pips_result' => $this->pips_result,
            'notes' => $this->notes,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
