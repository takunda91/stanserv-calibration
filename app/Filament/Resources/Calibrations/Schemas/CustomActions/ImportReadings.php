<?php

namespace App\Filament\Resources\Calibrations\Schemas\CustomActions;

use App\Enums\CalibrationStatus;
use App\Imports\ReadingsImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportReadings
{
    public static function make(): Action
    {

        return Action::make('importReadings')
            ->icon(Heroicon::DocumentArrowUp)
            ->color('primary')
            ->visible(fn($record) => $record->readings->count() > 0 && $record->status === CalibrationStatus::in_progress)
            ->schema([
                Select::make('compartment_number')
                    ->label('Select Compartment')
                    ->options(fn($record) => $record->compartments->pluck('number', 'number'))
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->helperText('Choose which compartment these readings belong to'),

                FileUpload::make('file')
                    ->required()
                    ->disk('public')
                    ->maxSize(1024 * 1024)
                    ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) // Accept CSV, XLS, XLSX by MIME type
                    ->hintAction(
                        Action::make('download_sample')
                            ->action(self::sample(...))
                    )
            ])->action(self::handle(...));
    }

    private static function handle(array $data, $record): void
    {
        try {
            \DB::transaction(static function () use ($data, $record) {
                $record->readings()->where('compartment_number', $data['compartment_number'])->delete();
                \Excel::import(new ReadingsImport($record->id, $data['compartment_number'] ), $data['file'], disk: 'public');

                Notification::make('note')
                    ->title('Readings Imported')
                    ->body('Readings imported successfully.')
                    ->success()
                    ->send();
            });

        } catch (\Throwable $e) {
            Notification::make('note')
                ->title('Readings Failed')
                ->body('Readings Failed : ' . $e->getMessage())
                ->danger()
                ->send();
        }


    }

    private static function sample(): StreamedResponse
    {
        $headers = ['dip_mm', 'volume'];
        $examples = [
            ['324', '1900'],
            ['340', '2000'],
            ['356', '2100'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($examples as $row) {
            $csv .= implode(',', $row) . "\n";
        }

        return response()->streamDownload(
            fn() => print($csv),
            'calibration-template.csv',
            ['Content-Type' => 'text/csv']
        );
    }
}
