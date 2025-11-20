<?php

namespace App\Services;

use App\Models\CalibrationReading;
use App\Models\CalibrationReadingInterpolation;
use App\Services\InterpolationCalculator\InterPolationCalculator;

class InterpolationService
{


    /**
     * @throws \Throwable
     */
    public function processInterpolation(int $calibrationId, int $compartmentNumber): void
    {
        $compartmentCalibrationReadings = CalibrationReading::where('calibration_id', $calibrationId)
            ->where('compartment_number', $compartmentNumber)
            ->orderBy('compartment_number')
            ->orderBy('volume')
            ->get();

        \DB::transaction(function () use ($compartmentCalibrationReadings, $compartmentNumber, $calibrationId) {
            foreach ($compartmentCalibrationReadings as $key => $compartmentReading) {
                $nextReading = $compartmentCalibrationReadings->get($key + 1);

                $upperDip = $nextReading?->dip_mm;
                $upperVol = $nextReading?->volume;
                $loweDip = $compartmentReading?->dip_mm;
                $loweVol = $compartmentReading?->volume;
                if (is_null($upperDip)) {
                    $this->persistInterpolation($calibrationId, $compartmentNumber, $loweDip, $loweVol, $compartmentReading->id, null);
                    continue;
                }
                $interpolationCalculator = new InterpolationCalculator($upperDip, $loweDip, $upperVol, $loweVol);
                $average = $interpolationCalculator->calculateInterpolationAverage();
                $numberOfGaps = $interpolationCalculator->calculateNumberOfInterpolationGaps();

                $this->persistInterpolation($calibrationId, $compartmentNumber, $loweDip, $loweVol, $compartmentReading->id, $average);

                if ($numberOfGaps > 0 && $average > 0) {
                    $startInterDip = $loweDip;
                    $startInterVol = $loweVol;
                    for ($i = 0; $i < $numberOfGaps; $i++) {
                        $nextVol = $startInterVol + 100;
                        $nextDip = $startInterDip + $average;
                        $this->persistInterpolation($calibrationId, $compartmentNumber, $nextDip, $nextVol, null, null);
                        $startInterVol = $nextVol;
                        $startInterDip = $nextDip;

                    }
                }
            }
        });

    }

    /**
     * @param int $calibrationId
     * @param int $compartmentNumber
     * @param float $dipmm
     * @param int $volume
     * @param $readingId
     * @param float|null $average
     * @return void
     */
    public function persistInterpolation(int $calibrationId, int $compartmentNumber, float $dipmm, int $volume, $readingId, ?float $average): void
    {
        $interpolationPersist = new CalibrationReadingInterpolation();
        $interpolationPersist->calibration_id = $calibrationId;
        $interpolationPersist->compartment_number = $compartmentNumber;
        $interpolationPersist->dip_mm = $dipmm;
        $interpolationPersist->volume = $volume;
        $interpolationPersist->run_by = auth()->id();
        $interpolationPersist->calibration_reading_id = $readingId;
        $interpolationPersist->interpolation_average = $average;
        $interpolationPersist->save();
    }


}
