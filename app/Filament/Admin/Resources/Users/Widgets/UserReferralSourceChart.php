<?php

namespace App\Filament\Admin\Resources\Users\Widgets;

use App\Enums\ReferralSource;
use App\Enums\Role;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserReferralSourceChart extends ChartWidget
{
    protected ?string $heading = 'Referral Sources';

    protected ?string $description = 'How users discovered the platform';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = User::where('role', Role::User)
            ->whereNotNull('referral_source')
            ->selectRaw('referral_source, count(*) as count')
            ->groupBy('referral_source')
            ->pluck('count', 'referral_source');

        $labels = [];
        $data = [];

        foreach (ReferralSource::cases() as $case) {
            if ($counts->has($case->value)) {
                $labels[] = $case->name;
                $data[] = (int) $counts[$case->value];
            }
        }

        $colors = [
            'rgb(99, 102, 241)',
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(245, 158, 11)',
            'rgb(239, 68, 68)',
            'rgb(168, 85, 247)',
            'rgb(20, 184, 166)',
            'rgb(236, 72, 153)',
            'rgb(34, 197, 94)',
            'rgb(249, 115, 22)',
            'rgb(107, 114, 128)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
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
