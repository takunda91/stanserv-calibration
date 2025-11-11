<?php

namespace App\Filament\Resources\Calibrations;

use App\Enums\CalibrationStatus;
use App\Filament\Resources\Calibrations\Pages\CalibrationReadings;
use App\Filament\Resources\Calibrations\Pages\CreateCalibration;
use App\Filament\Resources\Calibrations\Pages\EditCalibration;
use App\Filament\Resources\Calibrations\Pages\ListCalibrations;
use App\Filament\Resources\Calibrations\Pages\ViewCalibration;
use App\Filament\Resources\Calibrations\Schemas\CalibrationForm;
use App\Filament\Resources\Calibrations\Schemas\CalibrationInfolist;
use App\Filament\Resources\Calibrations\Tables\CalibrationsTable;
use App\Filament\Resources\Trucks\Pages\EditTruck;
use App\Models\Calibration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalibrationResource extends Resource
{
    protected static ?string $model = Calibration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;

    public static function form(Schema $schema): Schema
    {
        return CalibrationForm::configure($schema);
    }

    /**
     * @throws \Exception
     */
    public static function infolist(Schema $schema): Schema
    {
        return CalibrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalibrationsTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalibrations::route('/'),
            'create' => CreateCalibration::route('/create'),
            'edit' => EditCalibration::route('/{record}/edit'),
            'view' => ViewCalibration::route('/{record}'),
            'readings' => CalibrationReadings::route('/{record}/readings'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canEdit($record): bool
    {
       return $record->status === CalibrationStatus::pending;
    }



}
