<?php

namespace App\Filament\Resources\Calibrations\Tables;

use App\Enums\CalibrationStatus;
use App\Filament\Resources\Calibrations\Pages\CalibrationReadings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CalibrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('truck.reg_info')->searchable()->sortable(),
                TextColumn::make('technicians_list')->label('Technicians')->searchable()->sortable(),
                TextColumn::make('calibration_date')->date()->searchable()->sortable(),
                TextColumn::make('calibration_number')->searchable()->sortable(),
                TextColumn::make('calibrate_using')->searchable()->sortable(),
                TextColumn::make('status')->color(fn($state) => $state->color())->formatStateUsing(fn($state) => $state->label())->searchable()->sortable(),
                TextColumn::make('number_of_compartments')->label('Compartments')->searchable()->state(fn($record) => $record->compartments->count())->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->visible(fn($record) => $record->status === CalibrationStatus::pending),
                    ViewAction::make(),
                    RestoreAction::make(),
                    Action::make('Start Calibration')->color('success')
                        ->icon('heroicon-s-check')->visible(fn($record) => $record->status === CalibrationStatus::pending)
                        ->requiresConfirmation()
                        ->action(function($record){

                            if($record->compartments->count() === 0){
                                Notification::make()
                                    ->title('Calibration Error')
                                    ->color('danger')
                                    ->body('You must have at least one compartment')->danger()->send();
                                return;
                            }

                            $record->status = CalibrationStatus::in_progress;
                            $record->save();
                           Notification::make()->title('Calibration Started')->success()->send();
                        }),
                    Action::make('readings')->label('Readings')->visible(fn($record) => $record->status === CalibrationStatus::in_progress)
                    ->color('info')->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->url(fn($record) => CalibrationReadings::getUrl(['record' => $record]))
                ])->icon(Heroicon::EllipsisHorizontal)


            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
