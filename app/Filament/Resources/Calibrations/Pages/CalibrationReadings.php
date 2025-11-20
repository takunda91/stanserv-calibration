<?php

namespace App\Filament\Resources\Calibrations\Pages;

use App\Filament\Resources\Calibrations\CalibrationResource;
use App\Filament\Resources\Calibrations\Schemas\CustomActions\ImportReadings;
use App\Models\Calibration;
use App\Models\CalibrationReading;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\NoReturn;
use Tiptap\Nodes\Text;

class CalibrationReadings extends Page
{
    use InteractsWithRecord, InteractsWithForms;

    protected static string $resource = CalibrationResource::class;

    protected string $view = 'filament.resources.calibrations.pages.calibration-readings';

    public string|int|null|Model $record;
    public array $data = [];

    #[NoReturn]
    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $grouped = $this->record->readings
            ->groupBy('compartment_number')
            ->mapWithKeys(fn($group, $i) => [
                "compartment_{$i}_readings" => $group->map(fn($r) => [
                    'id' => $r->id,
                    'dip_mm' => $r->dip_mm,
                    'volume_l' => $r->volume,
                ])->toArray(),
            ])
            ->toArray();

        $this->form->fill($grouped);
    }


    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Calibration Header')->columns(3)
                    ->headerActions([
                        ViewAction::make()->color('primary'),
                        ImportReadings::make()
                    ])
                    ->schema([
                        TextEntry::make('truck_owner')->state($this->record->truck->owner_name)->label('Client'),
                        TextEntry::make('calibration_date')->state($this->record->calibration_date)->date()->label('Calibration Date'),
                        TextEntry::make('reg_numbers')->state($this->record->truck->reg_info)->label('Reg Numbers'),
                        TextEntry::make('technicians')->state($this->record->technicians_list)->label('Technicians'),
                        TextEntry::make('remarks')->state($this->record->meta['remarks'])->label('Remarks'),
                    ]),
                Tabs::make('Compartment Readings')
                    ->tabs(function () {
                        $compartments = $this->record->compartments;
                        $tabs = [];
                        foreach ($compartments as $compartment) {
                            $tabs[] = Tabs\Tab::make("Compartment $compartment->number")
                                ->badge($compartment->readings()->count())->badgeColor('warning')
                                ->icon(Heroicon::Truck)
                                ->schema([
                                    Repeater::make("compartment_{$compartment->number}_readings")
                                        ->label("Readings for Compartment $compartment->number")
                                        ->schema([
                                            Hidden::make('id')->dehydrated(),
                                            TextInput::make('dip_mm')
                                                ->label('Dip (mm)')
                                                ->numeric()
                                                ->required(),
                                            TextInput::make('volume_l')
                                                ->label('Volume (L)')
                                                ->numeric()
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->addActionLabel('Add Reading'),
                                ]);
                        }
                        return $tabs;
                    })
            ]);
    }

    public function saveReading()
    {
        $data = $this->form->getState();
        $readingIdsToKeep = [];

        foreach ($data as $key => $readings) {
            if (!str_starts_with($key, 'compartment_')) continue;

            preg_match('/compartment_(\d+)_readings/', $key, $matches);
            $compartmentNo = $matches[1] ?? null;
            foreach ($readings as $reading) {
                if (isset($reading['id']) && $reading['id']) {
                    // Update existing reading
                    $calibrationReading = CalibrationReading::find($reading['id']);
                    if ($calibrationReading) {
                        $calibrationReading->update([
                            'dip_mm' => $reading['dip_mm'],
                            'volume' => $reading['volume_l'],
                        ]);
                        $readingIdsToKeep[] = $reading['id'];
                    }
                } else {
                    // Create new reading
                    $calibrationReading = CalibrationReading::create([
                        'calibration_id' => $this->record->id,
                        'compartment_number' => $compartmentNo,
                        'dip_mm' => $reading['dip_mm'],
                        'volume' => $reading['volume_l'],
                        'captured_by' => auth()->user()->id,
                    ]);
                    $readingIdsToKeep[] = $calibrationReading->id;
                }
            }
        }


// Delete readings that were removed from repeaters
        CalibrationReading::where('calibration_id', $this->record->id)
            ->whereNotIn('id', $readingIdsToKeep)
            ->delete();

        Notification::make()->title('Readings Saved Successfully')->body('Readings Saved Successfully')
            ->success()->send();
    }


}
