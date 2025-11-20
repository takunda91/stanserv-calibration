<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Calibrations\Tables\CalibrationsTable;
use App\Models\Calibration;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestCalibrations extends TableWidget
{
    protected static ?int $sort = 5;
    protected static ?string $heading = 'Recent Calibrations (5)';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Calibration::query()->latest()->limit(5))
            ->defaultPaginationPageOption(5)
            ->paginated(false)
            ->columns(CalibrationsTable::calibrationList());
    }
}
