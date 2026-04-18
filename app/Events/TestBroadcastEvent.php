<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestBroadcastEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $message = 'Hello from Reverb!',
        public string $source = 'web-route',
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('test-broadcast'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'test.message';
    }
}
