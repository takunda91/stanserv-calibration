<?php

namespace App\Filament\Widgets;

use App\Models\CalibrationReadingInterpolation;
use Filament\Widgets\ChartWidget;

class NetInterpolationCyclew extends ChartWidget
{
    protected ?string $heading = 'Net Interpolation Cycle';

    protected static ?int $sort = 4;


    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {

        $readings = CalibrationReadingInterpolation::whereNotNull('interpolation_average')
            ->orderBy('volume')
            ->get();

        if ($readings->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Global Volume vs Average',
                    'data' => $readings->map(fn($r) => [
                        'x' => number_format($r->volume, 2),
                        'y' => number_format($r->interpolation_average, 2),
                    ])->toArray(),
                ],
            ],
//            'label' => 'Volume vs Average',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
