<?php

namespace App\Filament\Admin\Resources\Pools\Widgets;

use App\Enums\PoolStatus;
use App\Models\Pool;
use Filament\Widgets\ChartWidget;

class PoolCapitalChart extends ChartWidget
{
    protected ?string $heading = 'Capital per Pool';

    protected ?string $description = 'Total capital across active pools';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $pools = Pool::where('status', PoolStatus::ACTIVE)
            ->orderByDesc('total_amount')
            ->get(['name', 'total_amount']);

        return [
            'datasets' => [
                [
                    'label' => 'Capital (USD)',
                    'data' => $pools->map(fn (Pool $p): float => (float) $p->total_amount)->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.6)',
                    'borderColor' => 'rgb(99, 102, 241)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $pools->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
