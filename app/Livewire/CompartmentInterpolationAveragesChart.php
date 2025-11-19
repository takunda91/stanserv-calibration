<?php

namespace App\Livewire;

use App\Models\CalibrationCompartment;
use App\Models\CalibrationReadingInterpolation;
use Filament\Widgets\ChartWidget;

class CompartmentInterpolationAveragesChart extends ChartWidget
{
    protected ?string $heading = 'Compartment Interpolation Averages Chart';

    public ?int $compartmentId = null;

    protected function getData(): array
    {

        $compartment = CalibrationCompartment::findOrFail($this->compartmentId);

        $readings = CalibrationReadingInterpolation::where('compartment_number', $compartment->number)
            ->where('calibration_id', $compartment->calibration_id)
            ->whereNotNull('interpolation_average')
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
                    'label' => 'Volume vs Average',
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
