<?php

namespace App\Filament\Admin\Resources\Pools\Widgets;

use App\Enums\PoolStatus;
use App\Models\Pool;
use Filament\Widgets\ChartWidget;

class PoolsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Pools by Status';

    protected ?string $description = 'Distribution of pools across statuses';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Pool::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'datasets' => [
                [
                    'label' => 'Pools',
                    'data' => [
                        (int) ($counts[PoolStatus::ACTIVE->value] ?? 0),
                        (int) ($counts[PoolStatus::PAUSED->value] ?? 0),
                        (int) ($counts[PoolStatus::CLOSED->value] ?? 0),
                    ],
                    'backgroundColor' => [
                        'rgb(34, 197, 94)',    // Active — green
                        'rgb(245, 158, 11)',   // Paused — amber
                        'rgb(239, 68, 68)',    // Closed — red
                    ],
                ],
            ],
            'labels' => ['Active', 'Paused', 'Closed'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
