<?php

namespace App\Filament\Admin\Resources\Users\Widgets;

use App\Enums\Role;
use App\Models\User;
use Carbon\CarbonInterface;
use Filament\Widgets\ChartWidget;

class UserRegistrationsChart extends ChartWidget
{
    protected ?string $heading = 'User Registrations';

    protected ?string $description = 'Monthly registrations over the past 6 months';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $ago): CarbonInterface => now()->subMonths($ago)->startOfMonth());

        $registrations = User::where('role', Role::User)
            ->where('created_at', '>=', $months->first())
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->get()
            ->keyBy(fn ($r): string => $r->year.'-'.$r->month);

        return [
            'datasets' => [
                [
                    'label' => 'Registrations',
                    'data' => $months->map(fn (CarbonInterface $m): int => (int) ($registrations->get($m->year.'-'.$m->month)?->count ?? 0))->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.6)',
                    'borderColor' => 'rgb(99, 102, 241)',
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
