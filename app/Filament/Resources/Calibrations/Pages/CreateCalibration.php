<?php

namespace App\Filament\Resources\Calibrations\Pages;

use App\Enums\CalibrationStatus;
use App\Filament\Resources\Calibrations\CalibrationResource;
use App\Models\Calibration;
use Filament\Resources\Pages\CreateRecord;
use Log;

class CreateCalibration extends CreateRecord
{
    protected static string $resource = CalibrationResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = CalibrationStatus::pending;
        return $data;
    }

    protected function getFormActions(): array
    {
        return [$this->getCancelFormAction()];
    }

    protected function afterCreate(): void
    {
        $compartments = $this->form->getState()['compartments'] ?? [];
        Log::debug("Inside after create ", $compartments);
        if (!empty($compartments)) {
            $this->record->compartments()->createMany(
                collect($compartments)
                    ->map(fn($item, $index) => [
                        'compartment_number' => $index + 1,
                        'starting_volume' => $item['min_volume'],
                        'capacity' => $item['max_capacity'],
                    ])
                    ->toArray()
            );
        }
    }

}
