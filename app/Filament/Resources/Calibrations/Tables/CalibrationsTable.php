<?php

namespace App\Filament\Resources\Calibrations\Tables;

use App\Enums\CalibrationStatus;
use App\Filament\Resources\Calibrations\Pages\CalibrationReadings;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
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
                TextColumn::make('calibrate_using')->label('Calibration Type')->searchable()->sortable(),
                TextColumn::make('status')->color(fn($state) => $state->color())
                    ->formatStateUsing(fn($state) => $state->label())
                    ->searchable()->sortable(),
                TextColumn::make('number_of_compartments')->label('Comparts')->searchable()->state(fn($record) => $record->compartments->count())->sortable(),
                IconColumn::make('up_to_date')->label('Check List')->boolean()
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->visible(fn($record) => $record->status === CalibrationStatus::pending),
                    ViewAction::make(),
                    RestoreAction::make(),
                    self::startCalibrationAction(),
                    self::readingsViewAction(),
                    self::cancelCalibration(),
                    self::completeCalibration()
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

    /**
     * @return Action
     */
    public static function startCalibrationAction(): Action
    {
        return Action::make('Start Calibration')
            ->color('primary')
            ->icon(Heroicon::OutlinedClipboardDocumentList)
            ->visible(fn($record) => $record->status === CalibrationStatus::pending)
            ->requiresConfirmation()
            ->action(function ($record) {
                if ($record->compartments->count() === 0) {
                    Notification::make()
                        ->title('Calibration Error')
                        ->color('danger')
                        ->body('You must have at least one compartment')->danger()->send();
                    return;
                }

                $record->status = CalibrationStatus::in_progress;
                $record->save();
                Notification::make()->title('Calibration Started')->success()->send();
            });
    }

    /**
     * @return Action
     */
    public static function readingsViewAction(): Action
    {
        return Action::make('readings')
            ->label('Readings')
            ->visible(fn($record) => $record->status === CalibrationStatus::in_progress)
            ->color('info')->icon(Heroicon::OutlinedClipboardDocumentList)
            ->url(fn($record) => CalibrationReadings::getUrl(['record' => $record]));
    }

    private static function cancelCalibration(): Action
    {
        return Action::make('Cancel Calibration')
            ->color('danger')
            ->icon(Heroicon::OutlinedXMark)->visible(fn($record) => $record->status === CalibrationStatus::pending)
            ->requiresConfirmation()
            ->schema([
                TextInput::make('abort_reason')
            ])
            ->action(function ($record, array $data) {
               $record->aborted_reason = $data['abort_reason'];
               $record->abort_by = auth()->id();
               $record->abort_date = now();
               $record->status = CalibrationStatus::aborted;
               $record->save();

               Notification::make('Calibration Cancelled')
                   ->body("Calibration has been cancelled successfully.")
                   ->success()->send();
            });
    }

    private static function completeCalibration(): Action
    {
        return Action::make('Complete Calibration')
            ->color('success')
            ->icon(Heroicon::OutlinedDocumentCheck)
            ->visible(fn($record) => $record->status === CalibrationStatus::in_progress && $record->up_to_date)
            ->requiresConfirmation()
            ->schema([
                Select::make('sign_off_list.vehicle_checked_by')
                    ->label('Vehicle Checked by')
                    ->preload()
                    ->searchable()
                    ->options(fn() => User::role('technician')->pluck('name', 'id')),
                Select::make('sign_off_list.calibrated_by')
                    ->label('Calibrated By (Dipping)')
                    ->preload()
                    ->searchable()
                    ->options(fn() => User::role('technician')->pluck('name', 'id')),
                Select::make('sign_off_list.meter_controlled_by')
                    ->preload()
                    ->searchable()
                    ->options(fn() => User::role('technician')->pluck('name', 'id')),
                Select::make('sign_off_list.sticker_put_by')
                    ->preload()
                    ->searchable()
                    ->options(fn() => User::role('technician')->pluck('name', 'id')),
            ])
            ->action(function ($record, array $data) {
                $record->sign_off_list = $data['sign_off_list'];
                $record->complete_by = auth()->id();
                $record->complete_date = now();
                $record->status = CalibrationStatus::completed;
                $record->save();

                Notification::make('Calibration Completed')
                    ->body("Calibration has been completed successfully.")
                    ->success()->send();
            });
    }
}
