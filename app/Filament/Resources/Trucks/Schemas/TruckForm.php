<?php

namespace App\Filament\Resources\Trucks\Schemas;

use App\Enums\AirBagsConfig;
use App\Enums\TankShapes;
use App\Enums\TruckSuspensionTypes;
use App\Enums\TruckTypes;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TruckForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Truck Configuration')
                            ->description('Configure the truck and attach trailer information')
                            ->columns(3)
                            ->schema([
                                TextInput::make('horse_reg')
                                    ->label('Horse Registration')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                TextInput::make('trailer_reg')
                                    ->label('Trailer Registration')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Select::make('truck_type')
                                    ->label('Truck Type')
                                    ->options(TruckTypes::class)
                                    ->default(null),
                                Select::make('tank_shape')
                                    ->options(TankShapes::class)
                                    ->default(null),
                                Select::make('truck_suspension_type')
                                    ->options(TruckSuspensionTypes::class)
                                    ->default(null),
                                Select::make('air_bags')
                                    ->options(AirBagsConfig::class)
                                    ->default(null),
                            ]),
                        Section::make('Truck Details')
                            ->description('Add truck data')
                            ->columns(3)
                            ->schema([
                                TextInput::make('make')->required()
                                    ->default(null),
                                TextInput::make('model')->required()
                                    ->default(null),
                                TextInput::make('year')
                                    ->numeric()
                                    ->default(null),
                                TextInput::make('horse_chassis_number')->required()
                                    ->default(null),
                                TextInput::make('engine_number')->required()
                                    ->default(null),
                                TextInput::make('trailer_chassis_number')->required()
                                    ->default(null),
                                TextInput::make('road_license_number')
                                    ->default(null),
                            ]),
                        Section::make('Owner Details')
                            ->description('Maintain Owner Information')
                            ->columns(3)
                            ->schema([
                                TextInput::make('owner_name')->required()
                                    ->default(null),
                                TextInput::make('owner_address')
                                    ->default(null),
                                TextInput::make('owner_phone')
                                    ->tel()
                                    ->default(null),
                                TextInput::make('owner_cell')
                                    ->default(null),
                                TextInput::make('owner_email')
                                    ->email()
                                    ->default(null),
                                TextInput::make('owner_contact_name')
                                    ->default(null),
                                TextInput::make('owner_contact_phone')
                                    ->tel()
                                    ->default(null),
                                TextInput::make('owner_driver_name')
                                    ->default(null),
                                TextInput::make('owner_driver_phone')
                                    ->tel()
                                    ->default(null)
                            ])

                    ])
            ]);
    }
}
