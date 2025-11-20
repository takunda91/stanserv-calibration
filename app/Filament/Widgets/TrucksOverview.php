<?php

namespace App\Filament\Widgets;

use App\Enums\TruckTypes;
use App\Models\Truck;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrucksOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected ?string $heading = 'Trucks';
    protected function getStats(): array
    {
        return [
            Stat::make('Total Trucks', Truck::count())
                ->description('All Trucks')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('primary'),
            Stat::make(TruckTypes::RIGID->value, Truck::where('truck_type', TruckTypes::RIGID)->count())
                ->description('Type Rigid')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('warning'),
            Stat::make(TruckTypes::ARTICULATED->value, Truck::where('truck_type', TruckTypes::ARTICULATED)->count())
                ->description('Type Article')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),
        ];
    }
}
