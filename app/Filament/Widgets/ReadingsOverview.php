<?php

namespace App\Filament\Widgets;

use App\Models\CalibrationReading;
use App\Models\CalibrationReadingInterpolation;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReadingsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'Readings / Interpolations Summary';
    protected function getStats(): array
    {
        $interpolations = CalibrationReadingInterpolation::count();
        $readings = CalibrationReading::count();
        return [
            Stat::make('All Readings To Date', $readings)
                ->description('All Readings')
                ->descriptionIcon(Heroicon::OutlinedCalculator, IconPosition::Before),
            Stat::make('Interpolations To Date', $interpolations)
                ->description('All Interpolations')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('warning'),
            Stat::make('Total Interpolations In-Processed', $interpolations - $readings)
                ->description('Processed')
                ->descriptionIcon(Heroicon::OutlinedPrinter, IconPosition::Before)
                ->color('success'),
        ];
    }
}
