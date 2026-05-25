<?php

namespace App\Filament\Admin\Resources\TradingClasses\Widgets;

use App\Models\TradingClass;
use Carbon\CarbonInterface;
use Filament\Widgets\ChartWidget;

class TradingClassesPerMonthChart extends ChartWidget
{
    protected ?string $heading = 'Classes Scheduled';

    protected ?string $description = 'Classes scheduled per month over the past 6 months';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $ago): CarbonInterface => now()->subMonths($ago)->startOfMonth());

        $classes = TradingClass::where('scheduled_at', '>=', $months->first())
            ->selectRaw('EXTRACT(YEAR FROM scheduled_at) as year, EXTRACT(MONTH FROM scheduled_at) as month, count(*) as count')
            ->groupByRaw('EXTRACT(YEAR FROM scheduled_at), EXTRACT(MONTH FROM scheduled_at)')
            ->get()
            ->keyBy(fn ($r): string => $r->year.'-'.$r->month);

        return [
            'datasets' => [
                [
                    'label' => 'Classes',
                    'data' => $months->map(fn (CarbonInterface $m): int => (int) ($classes->get($m->year.'-'.$m->month)?->count ?? 0))->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $months->map(fn (CarbonInterface $m): string => $m->format('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
