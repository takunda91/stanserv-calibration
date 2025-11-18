<?php

namespace App\Filament\Resources\Calibrations\Schemas;

use App\Filament\Resources\Calibrations\Pages\CalibrationReadings;
use App\Services\InterpolationService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;

class CalibrationInfolist
{

    /**
     * @throws \Exception
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Tabs::make()
                        ->tabs([
                            Tab::make('Calibration Information')->icon(Heroicon::ServerStack)
                                ->schema(self::buildHeader()),
                            Tab::make('Compartments')->icon(Heroicon::BuildingStorefront)
                                ->badge(fn($record) => $record->compartments->count())
                                ->schema([
                                    RepeatableEntry::make('compartments')
                                        ->hiddenLabel()->columnSpanFull()
                                        ->schema([
                                            TextEntry::make('number')->label('Compartment Number')->numeric(),
                                            TextEntry::make('starting_volume')->label('Starting Volume (L)')->numeric(),
                                            TextEntry::make('capacity')->label('Max Capacity (L)')->numeric(),
                                        ])->columns(3)
                                ]),
                            Tab::make('Readings')
                                ->badge(fn($record) => $record->readings->count())
                                ->badgeColor('warning')
                                ->icon(Heroicon::Calculator)
                                ->schema([
                                    Section::make('Readings [Compartments 1 - 3]')->visible(fn($record) => $record?->readings)
                                        ->headerActions([
                                            Action::make('readings')
                                                ->label('Readings')
                                                ->color('info')
                                                ->icon(Heroicon::Plus)
                                                ->url(fn($record) => CalibrationReadings::getUrl(['record' => $record]))
                                        ])
                                        ->collapsible()
                                        ->collapsed()
                                        ->schema(function ($record) {
                                            // Group readings by compartment_no
                                            if (!$record) {
                                                return [];
                                            }

                                            $compartments = $record->readings
                                                ->groupBy('compartment_number')
                                                ->sortKeys();

                                            return [
                                                Section::make()
                                                    ->headerActions([
                                                        Action::make('openReadingsPage')
                                                            ->label('Manage Readings')
                                                            ->color('info')
                                                            ->icon(Heroicon::ArrowRight)
                                                            ->url(fn($record) => CalibrationReadings::getUrl(['record' => $record])),
                                                    ])
                                                    ->schema([
                                                        Grid::make()
                                                            ->columns(2) // auto-expand, safe default
                                                            ->schema(
                                                                $compartments->map(function ($readings, $compartment) {

                                                                    return Section::make("Compartment {$compartment}")
                                                                        ->collapsible()
                                                                        ->collapsed()
                                                                        ->schema([
                                                                            ViewEntry::make("compartment_{$compartment}_readings")
                                                                                ->view('filament.resources.calibrations.pages.readings-table')
                                                                                ->viewData([
                                                                                    'readings' => $readings,
                                                                                ])
                                                                        ]);
                                                                })->values()->all()
                                                            )
                                                    ])
                                            ];
                                        }),
                                ]),
                            Tab::make('Interpolation')
                                ->badge(fn($record) => $record->interpolations->count())
                                ->badgeColor('danger')
                                ->icon(Heroicon::ShieldExclamation)
                                ->schema([
                                    Section::make('Interpolation [Compartments 1 - 3]')->visible(fn($record) => $record?->readings)
                                        ->headerActions([
                                            Action::make('interpolation')
                                                ->label('Run Interpolation')
                                                ->icon(Heroicon::Plus)
                                                ->action(function () {
                                                    $interpolationService = new InterpolationService();
                                                    dd($interpolationService->processInterpolation(3, 1));
                                                })
                                        ])
                                        ->collapsible()
                                        ->collapsed()
                                        ->schema(function ($record) {
                                            // Group readings by compartment_no
                                            if (!$record) {
                                                return [];
                                            }

                                            return [Grid::make(3)
                                                ->schema(
                                                    $record->readings
                                                        ->groupBy('compartment_number')
                                                        ->map(fn($readings, $compartment) => Section::make("Compartment  " . $compartment)
                                                            ->schema([
                                                                TableRepeatableEntry::make('readings')
                                                                    ->hiddenLabel()
                                                                    ->schema([
                                                                        TextEntry::make('dip_mm')->alignCenter()->label('Dip (mm)'),
                                                                        TextEntry::make('volume')->alignCenter()->label('Volume (L)'),
                                                                    ])
                                                                    ->state($readings->toArray())
                                                                    ->columns(2)
                                                            ])
                                                        )
                                                        ->values()
                                                        ->all()
                                                )];

                                        }),
                                ]),
                            Tab::make('Certificate')->icon(Heroicon::Newspaper)
                                ->schema([])
                        ])
                ])->columnSpanFull()
            ]);
    }

    protected static function buildHeader(): array
    {
        return [
            Section::make('Calibration Header')
                ->schema([
                    TextEntry::make('truck.reg_info')->label('Truck Details'),
                    TextEntry::make('calibration_number'),
                    TextEntry::make('calibrate_using'),
                    TextEntry::make('calibration_date')
                        ->date(),
                    TextEntry::make('coupling_height_before')->default('n/a')
                        ->numeric(),
                    TextEntry::make('technicians_list')->label('Technicians'),
                    TextEntry::make('meta.remarks')
                ])->columnSpanFull()
                ->collapsible()
                ->columns(3)->compact(),
            Section::make('Permit To Work')
                ->columns()
                ->schema([
                    Fieldset::make('Pre-Calibration Safety Checks')->columnSpanFull()->columns(3)
                        ->schema([
                            IconEntry::make('permit_to_work.ppe_worn')->label('PPE Worn')->boolean(),
                            IconEntry::make('permit_to_work.safety_harness_properly_worn')->boolean()->label('Safety Harness Properly Worn'),
                            IconEntry::make('permit_to_work.distractions_removed')->boolean()->label('Distractions Removed'),
                            IconEntry::make('permit_to_work.correct_equipment_available')->boolean()->label('Correct Equipment Available'),
                            IconEntry::make('permit_to_work.hazards_identified')->boolean()->label('Have All Hazards Been Identified'),
                            IconEntry::make('permit_to_work.residual_fuel_product_drained')->boolean()
                                ->label('Residual Fuel Or Product Drained'),
                            IconEntry::make('permit_to_work.proper_ladder_on_truck')->boolean()->label('Is There A Proper Ladder Fitted On Truck'),
                            IconEntry::make('permit_to_work.wheel_chokes_on_truck')->boolean()->label('Are Wheel Chokes Applied On Truck'),
                            IconEntry::make('permit_to_work.guard_rails_fitted')->boolean()->label('Are Guard Rails Fitted'),
                            IconEntry::make('permit_to_work.toxic_and_corrosive_hazards')->boolean()->label('Toxic And Corrosive Hazards Considered'),
                            IconEntry::make('permit_to_work.potential_environmental_impact')->boolean()->label('Potential Environmental Impact Considered'),
                            IconEntry::make('permit_to_work.water_leaks')->boolean()->label('Are There Water Leaks'),
                            IconEntry::make('permit_to_work.equipment_preuse_inspection')->boolean()->label('Equipment Pre-use Inspection done'),
                            IconEntry::make('permit_to_work.truck_pneumatics_system_working')->boolean()->label('Is Truck Pneumatics System In Good Order'),
                        ])
                ])->columnSpanFull()
                ->collapsible()
                ->columns(4)->compact(),
            Section::make('Risk Assessment')
                ->columns()
                ->schema([
                    Fieldset::make('Hazard Identification  and Risk Assessment')->columnSpanFull()->columns(3)
                        ->schema([
                            IconEntry::make('risk_assessment.flammable_vapours')->boolean()->label('Flammable Vapours'),
                            IconEntry::make('risk_assessment.electricity')->boolean()->label('Electricity'),
                            IconEntry::make('risk_assessment.elevated_positions')->boolean()->label('Elevated Positions'),
                            IconEntry::make('risk_assessment.flammable_liquids')->boolean()->label('Flammable Liquids'),
                            IconEntry::make('risk_assessment.pressurized_substances')->boolean()->label('Pressurized Substances'),
                            IconEntry::make('risk_assessment.noise')->boolean()->label('Noise'),
                            IconEntry::make('risk_assessment.dust')->boolean()->label('Dust'),
                            IconEntry::make('risk_assessment.inhalation_of_vapours')->boolean()->label('Inhalation of Vapours'),
                            IconEntry::make('risk_assessment.moving_vehicles_and_machinery')->boolean()->label('Moving Vehicles and Machinery'),
                            IconEntry::make('risk_assessment.slippery_surfaces')->boolean()->label('Slippery Surfaces'),
                            IconEntry::make('risk_assessment.chemical_exposures')->boolean()->label('Chemical Exposures')
                        ])
                ])->columnSpanFull()
                ->collapsible()
                ->columns(4)->compact(),

            Section::make('Pre Calibration Safety Checklist')
                ->columns()
                ->schema([
                    Fieldset::make('Pre-Calibration Safety Checks')->columnSpanFull()->columns(3)
                        ->schema([
                            IconEntry::make('precheck.general_conditions_satisfactory')->boolean()->label('General Conditions Satisfactory'),
                            IconEntry::make('precheck.electrics_satisfactory')->boolean()->label('Electrics Satisfactory'),
                            IconEntry::make('precheck.brakes')->boolean()->label('Brakes'),
                            IconEntry::make('precheck.fire_extinguisher')->boolean()->label('Fire Extinguisher Available'),
                            IconEntry::make('precheck.pneumatics_satisfactory')->boolean()->label('Pneumatics Satisfactory'),
                            IconEntry::make('precheck.last_service_known')->boolean()->label('Last Service Known')->default(false)->live(),
                            TextEntry::make('precheck.last_service_date')->visible(fn(Get $get) => $get('precheck.last_service_known') == true)
                                ->label('Last Service Date'),

                        ])
                ])->columnSpanFull()
                ->collapsible()
                ->columns(4)->compact()
        ];
    }
}
