<?php

namespace App\Filament\Widgets;

use App\Enums\CalibrationStatus;
use App\Models\Calibration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CalibrationOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Calibration Summary';
    protected function getStats(): array
    {
        return [
            Stat::make('Total Calibrations', Calibration::count())
                ->description('Total Calibrations')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
            Stat::make('In Progress', Calibration::where('status', CalibrationStatus::in_progress)->count())
                ->description('Calibrations In Progress')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
            Stat::make('Completed', Calibration::where('status', CalibrationStatus::completed)->count())
                ->description('Calibrations Completed')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
