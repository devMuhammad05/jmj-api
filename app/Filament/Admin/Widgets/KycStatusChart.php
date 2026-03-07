<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\VerificationStatus;
use App\Models\Verification;
use Filament\Widgets\ChartWidget;

class KycStatusChart extends ChartWidget
{
    protected ?string $heading = 'KYC Verification Status';

    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $pending = Verification::where('status', VerificationStatus::PENDING)->count();
        $approved = Verification::where('status', VerificationStatus::APPROVED)->count();
        $rejected = Verification::where('status', VerificationStatus::REJECTED)->count();

        return [
            'datasets' => [
                [
                    'label' => 'KYC Status',
                    'data' => [$pending, $approved, $rejected],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)', // Yellow for pending
                        'rgb(34, 197, 94)',  // Green for approved
                        'rgb(239, 68, 68)',  // Red for rejected
                    ],
                ],
            ],
            'labels' => ['Pending', 'Approved', 'Rejected'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
