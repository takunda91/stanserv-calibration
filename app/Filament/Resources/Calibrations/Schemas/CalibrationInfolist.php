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
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
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
                                            TextEntry::make('number')->label('Compartment #')->numeric(),
                                            TextEntry::make('starting_volume')->label('Starting Volume (L)')->numeric(),
                                            TextEntry::make('capacity')->label('Max Capacity (L)')->numeric(),
                                            IconEntry::make('is_updated')->label('Readings Processed')->state(fn($record) => $record->is_read_same_as_interpolated)->boolean(),
                                            Actions::make([
                                                self::generateReadingsAction(),
                                                self::generateInterpolationsAction(),
                                                self::generateInterpolationChartFlow()
                                            ])->visible(fn($record) => $record->readings->count() > 0),
                                        ])->columns(5)
                                ]),
                            Tab::make('Readings')
                                ->badge(fn($record) => $record->readings->count())
                                ->badgeColor('warning')
                                ->icon(Heroicon::Calculator)
                                ->schema([
                                    self::readingsTabData()
                                ]),
                            Tab::make('Interpolation')
                                ->badge(fn($record) => $record->interpolations->count())
                                ->badgeColor('danger')
                                ->icon(Heroicon::ShieldExclamation)
                                ->schema([
                                    self::interpolationTabData(),
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

    /**
     * @return Action
     */
    public static function generateReadingsAction(): Action
    {
        return Action::make('viewReadings')
            ->label(fn($record) => (string)($record->readings->count()))
            ->icon(Heroicon::OutlinedCalculator)
            ->tooltip('View Readings')
            ->modalHeading(fn($record) => "Compartment {$record->number} - Calibration Readings")
            ->modalContent(fn($record) => view('filament.resources.calibrations.pages.readings-table', [
                'compartment' => $record,
                'readings' => $record->readings()->orderBy('volume')->get(),
                'hasWidget' => true
            ]))->modalSubmitAction(false)
            ->slideOver();
    }

    public static function generateInterpolationsAction(): Action
    {
        return Action::make('viewInterpolations')
            ->label(fn($record) => (string)($record->interpolations->count()))
            ->icon(Heroicon::OutlinedShieldExclamation)
            ->tooltip('View Interpolations')
            ->modalHeading(fn($record) => "Compartment {$record->number} - Interpolations")
            ->modalContent(fn($record) => view('filament.resources.calibrations.pages.interpolations-table', [
                'compartment' => $record,
                'interpolations' => $record->interpolations()->orderBy('volume')->get(),
                'manual' => $record->readings(),
                'hasWidget' => true
            ]))->modalSubmitAction(false)
            ->slideOver();
    }

    private static function generateInterpolationChartFlow(): Action
    {
        return Action::make('interpolationChartFlow')
            ->label(fn($record) => (string)($record->interpolations->count()))
            ->icon(Heroicon::OutlinedChartPie)
            ->tooltip('View Interpolations Flow')
            ->color('success')
            ->modalHeading(fn($record) => "Compartment {$record->number} - Line Flow")
            ->modalContent(fn($record) => view('filament.compartment-interpolation-chart', [
                'compartment' => $record,
            ]))->modalSubmitAction(false)
            ->modalWidth('7xl')
            ->slideOver();
    }

    /**
     * @return Action
     */
    public static function calculateInterpolations(): Action
    {
        return Action::make('interpolation')
            ->label('Run Interpolation')
            ->color('success')
            ->visible(fn($record) => $record->readings()->count() > 0)
            ->icon(Heroicon::Plus)
            ->action(function ($record) {

                $record->interpolations()->delete();

                foreach ($record->compartments as $compartment) {
                    $interpolationService = new InterpolationService();
                    $interpolationService->processInterpolation($record->id, $compartment->number);
                }

                Notification::make('Interpolation_processed')
                    ->title('Interpolation Processed')
                    ->body('Interpolation Processed successfully.')
                    ->success()
                    ->send();

            });
    }


    /**
     * @return Section
     */
    public static function readingsTabData(): Section
    {
        return Section::make('Readings')
            ->headerActions([
                Action::make('readings')
                    ->label('Readings')
                    ->color('info')
                    ->visible(fn($record) => $record->readings->count() > 0)
                    ->icon(Heroicon::Plus)
                    ->url(fn($record) => CalibrationReadings::getUrl(['record' => $record]))
            ])
            ->collapsible()
            ->schema(function ($record) {
                // Group readings by compartment_no
                if (!$record) {
                    return [];
                }

                $compartments = $record->readings
                    ->groupBy('compartment_number')
                    ->sortKeys();

                return [
                    Grid::make()
                        ->columns(3)
                        ->schema(
                            $compartments->map(function ($readings, $compartment) {
                                return Section::make("Compartment {$compartment}")
                                    ->collapsible()
                                    ->schema([
                                        ViewEntry::make("compartment_{$compartment}_readings")
                                            ->view('filament.resources.calibrations.pages.readings-table')
                                            ->viewData([
                                                'readings' => $readings,
                                                'hasWidget' => false
                                            ])
                                    ]);
                            })->values()->all()
                        )
                ];
            });
    }

    /**
     * @return Section
     */
    public static function interpolationTabData(): Section
    {
        return Section::make('Interpolations')->visible(fn($record) => $record?->readings)
            ->headerActions([
                self::calculateInterpolations()
            ])
            ->collapsible()
            ->schema(function ($record) {
                // Group readings by compartment_no
                if (!$record) {
                    return [];
                }

                $compartments = $record->interpolations
                    ->groupBy('compartment_number')
                    ->sortKeys();
                return [
                    Grid::make()
                        ->columns(3)
                        ->schema(
                            $compartments->map(function ($interpolations, $compartment) {
                                return Section::make("Compartment {$compartment}")
                                    ->collapsible()
                                    ->schema([
                                        ViewEntry::make("compartment_{$compartment}_interpolations")
                                            ->view('filament.resources.calibrations.pages.interpolations-table')
                                            ->viewData([
                                                'interpolations' => $interpolations,
                                                'manual' => collect(),
                                                'hasWidget' => false
                                            ])
                                    ]);
                            })->values()->all()
                        )
                ];

            });
    }


}
