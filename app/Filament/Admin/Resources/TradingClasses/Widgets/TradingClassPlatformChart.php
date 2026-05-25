<?php

namespace App\Filament\Admin\Resources\TradingClasses\Widgets;

use App\Enums\ClassPlatform;
use App\Models\TradingClass;
use Filament\Widgets\ChartWidget;

class TradingClassPlatformChart extends ChartWidget
{
    protected ?string $heading = 'Classes by Platform';

    protected ?string $description = 'Distribution of classes across platforms';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = TradingClass::selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        $labels = [];
        $data = [];

        foreach (ClassPlatform::cases() as $case) {
            if ($counts->has($case->value)) {
                $labels[] = $case->name;
                $data[] = (int) $counts[$case->value];
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Classes',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',   // Zoom — blue
                        'rgb(99, 102, 241)',   // Telegram — indigo
                        'rgb(34, 197, 94)',    // Google Meet — green
                        'rgb(239, 68, 68)',    // YouTube — red
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
