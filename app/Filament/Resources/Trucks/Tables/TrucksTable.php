<?php

namespace App\Filament\Resources\Trucks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrucksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('horse_reg')
                    ->searchable(),
                TextColumn::make('trailer_reg')
                    ->searchable(),
                TextColumn::make('make')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('horse_chassis_number')
                    ->searchable(),
                TextColumn::make('engine_number')
                    ->searchable(),
                TextColumn::make('trailer_chassis_number')
                    ->searchable(),
                TextColumn::make('road_license_number')
                    ->searchable(),
                TextColumn::make('owner_name')
                    ->searchable(),
                TextColumn::make('owner_address')
                    ->searchable(),
                TextColumn::make('owner_phone')
                    ->searchable(),
                TextColumn::make('owner_cell')
                    ->searchable(),
                TextColumn::make('owner_email')
                    ->searchable(),
                TextColumn::make('owner_contact_name')
                    ->searchable(),
                TextColumn::make('owner_contact_phone')
                    ->searchable(),
                TextColumn::make('owner_driver_name')
                    ->searchable(),
                TextColumn::make('owner_driver_phone')
                    ->searchable(),
                TextColumn::make('truck_type')
                    ->searchable(),
                TextColumn::make('tank_shape')
                    ->searchable(),
                TextColumn::make('truck_suspension_type')
                    ->searchable(),
                TextColumn::make('air_bags')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
