<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\SignalStatus;
use App\Models\Signal;
use Filament\Widgets\ChartWidget;

class SignalPerformanceChart extends ChartWidget
{
    protected ?string $heading = 'Signal Performance (Last 30 Days)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $signals = Signal::where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy(function ($signal) {
                return $signal->created_at->format('M d');
            });

        $labels = [];
        $hitTpData = [];
        $hitSlData = [];
        $activeData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('M d');
            $labels[] = $dateKey;

            $daySignals = $signals->get($dateKey, collect());

            $hitTpData[] = $daySignals->where('status', SignalStatus::TP)->count();
            $hitSlData[] = $daySignals->where('status', SignalStatus::SL)->count();
            $activeData[] = $daySignals->where('status', SignalStatus::ACTIVE)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hit TP (Win)',
                    'data' => $hitTpData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Hit SL (Loss)',
                    'data' => $hitSlData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Active',
                    'data' => $activeData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
