<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradingClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "scheduled_at" => $this->scheduled_at->toDateTimeString(),
            "formatted_date" => $this->scheduled_at->format("M j, Y"),
            "formatted_time" => $this->scheduled_at->format("g:i A"),
            "platform" => $this->platform->value,
            "platform_label" => ucfirst($this->platform->value),
            "meeting_link" => $this->meeting_link,
            "created_at" => $this->created_at->toDateTimeString(),
        ];
    }
}
