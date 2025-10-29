<?php

namespace App\Filament\Resources\Trucks\Pages;

use App\Filament\Resources\Trucks\TruckResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTruck extends CreateRecord
{
    protected static string $resource = TruckResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
