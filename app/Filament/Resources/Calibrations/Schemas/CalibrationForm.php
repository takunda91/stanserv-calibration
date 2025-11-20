<?php

namespace App\Filament\Resources\Calibrations\Schemas;

use App\Enums\CalibrateUsing;
use App\Enums\CalibrationStatus;
use App\Models\Calibration;
use App\Models\Truck;
use App\Models\User;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CalibrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Calibration Header')
                        ->columns(3)
                        ->schema([
                            Select::make('truck_id')
                                ->relationship('truck', modifyQueryUsing: fn(Builder $query) => $query->notInCalibration())
                                ->getOptionLabelFromRecordUsing(fn($record) => "$record->owner_name ($record->horse_reg | $record->trailer_reg)")
                                ->preload()
                                ->required(),
                            TextInput::make('calibration_number')
                                ->helperText('Will be used as certificate  number')
                                ->default(Calibration::generateCalibrationNumber())
                                ->unique()->required(),
                            Select::make('calibrate_using')
                                ->options(CalibrateUsing::class)
                                ->required(),
                            DatePicker::make('calibration_date'),
                            TextInput::make('coupling_height_before')->numeric(),
                            TextInput::make('number_of_compartments')->numeric()
                                ->dehydrated(false)
                                ->minValue(1)
                                ->live()
                                ->required()
                                ->afterStateHydrated(function (Get $get, Set $set, $state, Model $record = null) {
                                    $count = $record ? $record->compartments()->count() : 1;
                                    if (is_null($state) || $state === '') {
                                        $set('number_of_compartments', $count);
                                    }
                                }),
                            Select::make('meta.technicians')
                                ->label('Technicians')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->options(fn() => User::role('technician')->pluck('name', 'id'))
                                ->helperText('Select all technicians who handled this calibration'),
                            Textarea::make('meta.remarks')->label('Remarks')->columnSpanFull(),
                        ]),
                    Step::make('Compartments')
                        ->columns()->schema([
                            Repeater::make('compartments')
                                ->relationship()
                                ->dehydrated(false)
                                ->label('Compartments')
                                ->maxItems(fn(Get $get) => $get('number_of_compartments'))
                                ->minItems(fn(Get $get) => $get('number_of_compartments'))
                                ->schema([
                                    TextInput::make('number')
                                        ->numeric()
                                        ->readOnly() // Make it a read-only display
                                        ->label('Compartment Number'),
                                    TextInput::make('starting_volume')
                                        ->numeric()
                                        ->default(100)
                                        ->required()
                                        ->label('Minimum Volume'),
                                    TextInput::make('capacity')
                                        ->numeric()
                                        ->required()
                                        ->label('Maximum Capacity'),
                                ])->itemLabel("Compartment")
                                ->afterStateHydrated(function (Repeater $component, Set $set) {
                                    $state = $component->getState();
                                    if ($state) {
                                        $keys = array_keys($state);
                                        foreach ($keys as $index => $key) {
                                            $set("compartments.{$key}.number", $index + 1);
                                        }
                                    }
                                })

                                // This runs after any action (add, delete, reorder)
                                ->afterStateUpdated(function (Repeater $component, Set $set) {
                                    $state = $component->getState();
                                    if ($state) {
                                        $keys = array_keys($state);
                                        foreach ($keys as $index => $key) {
                                            $set("compartments.{$key}.number", $index + 1);
                                        }
                                    }
                                })->columnSpan(2)
                                ->columns(3)
                                ->live()
                        ]),
                    Step::make('Permit To Work')
                        ->columns()
                        ->schema([
                            Fieldset::make('Pre-Calibration Safety Checks')->columnSpanFull()
                                ->schema([
                                    Toggle::make('permit_to_work.ppe_worn')->label('PPE Worn'),
                                    Toggle::make('permit_to_work.safety_harness_properly_worn')->label('Safety Harness Properly Worn'),
                                    Toggle::make('permit_to_work.distractions_removed')->label('Distractions Removed'),
                                    Toggle::make('permit_to_work.correct_equipment_available')->label('Correct Equipment Available'),
                                    Toggle::make('permit_to_work.hazards_identified')->label('Have All Hazards Been Identified'),
                                    Toggle::make('permit_to_work.residual_fuel_product_drained')
                                        ->label('Residual Fuel Or Product Drained'),
                                    Toggle::make('permit_to_work.proper_ladder_on_truck')->label('Is There A Proper Ladder Fitted On Truck'),
                                    Toggle::make('permit_to_work.wheel_chokes_on_truck')->label('Are Wheel Chokes Applied On Truck'),
                                    Toggle::make('permit_to_work.guard_rails_fitted')->label('Are Guard Rails Fitted'),
                                    Toggle::make('permit_to_work.toxic_and_corrosive_hazards')->label('Toxic And Corrosive Hazards Considered'),
                                    Toggle::make('permit_to_work.potential_environmental_impact')->label('Potential Environmental Impact Considered'),
                                    Toggle::make('permit_to_work.water_leaks')->label('Are There Water Leaks'),
                                    Toggle::make('permit_to_work.equipment_preuse_inspection')->label('Equipment Pre-use Inspection done'),
                                    Toggle::make('permit_to_work.truck_pneumatics_system_working')->label('Is Truck Pneumatics System In Good Order'),
                                ])
                                ->columns(2)
                        ]),
                    Step::make('Risk Assessment')
                        ->columns()
                        ->schema([
                            Fieldset::make('Hazard Identification  and Risk Assessment')->columnSpanFull()
                                ->schema([
                                    Toggle::make('risk_assessment.flammable_vapours')->label('Flammable Vapours'),
                                    Toggle::make('risk_assessment.electricity')->label('Electricity'),
                                    Toggle::make('risk_assessment.elevated_positions')->label('Elevated Positions'),
                                    Toggle::make('risk_assessment.flammable_liquids')->label('Flammable Liquids'),
                                    Toggle::make('risk_assessment.pressurized_substances')->label('Pressurized Substances'),
                                    Toggle::make('risk_assessment.noise')->label('Noise'),
                                    Toggle::make('risk_assessment.dust')->label('Dust'),
                                    Toggle::make('risk_assessment.inhalation_of_vapours')->label('Inhalation of Vapours'),
                                    Toggle::make('risk_assessment.moving_vehicles_and_machinery')->label('Moving Vehicles and Machinery'),
                                    Toggle::make('risk_assessment.slippery_surfaces')->label('Slippery Surfaces'),
                                    Toggle::make('risk_assessment.chemical_exposures')->label('Chemical Exposures')
                                ])
                        ]),
                    Step::make('Pre Calibration Safety Checklist')
                        ->columns()
                        ->schema([
                            Fieldset::make('Pre-Calibration Safety Checks')->columnSpanFull()
                                ->schema([
                                    Toggle::make('precheck.general_conditions_satisfactory')->label('General Conditions Satisfactory'),
                                    Toggle::make('precheck.electrics_satisfactory')->label('Electrics Satisfactory'),
                                    Toggle::make('precheck.brakes')->label('Brakes'),
                                    Toggle::make('precheck.fire_extinguisher')->label('Fire Extinguisher Available'),
                                    Toggle::make('precheck.pneumatics_satisfactory')->label('Pneumatics Satisfactory'),
                                    Toggle::make('precheck.last_service_known')->label('Last Service Known')->default(false)->live(),
                                    DatePicker::make('precheck.last_service_date')->visible(fn(Get $get) => $get('precheck.last_service_known') == true)
                                        ->label('Last Service Date'),

                                ])
                                ->columns(2)
                        ]),

                ])->columnSpanFull()
                    ->submitAction(new HtmlString(Blade::render(<<<BLADE
                                    <x-filament::button
                                        type="submit"
                                        size="sm"
                                    >
                                        Submit
                                    </x-filament::button>
                                BLADE
                    )))
            ]);
    }
}
