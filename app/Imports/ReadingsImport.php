<?php

namespace App\Imports;

use App\Models\CalibrationReading;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReadingsImport implements ToModel, WithHeadingRow
{
    private int $calibrationId;
    private int $compartmentNumber;

    public function __construct(int $calibrationId, int $compartmentNumber)
    {
        $this->calibrationId = $calibrationId;
        $this->compartmentNumber = $compartmentNumber;
    }


    /**
     * @param array $row
     *
     * @return Model|CalibrationReading|null
     */
    public function model(array $row): Model|CalibrationReading|null
    {
        return new CalibrationReading([
            'calibration_id' => $this->calibrationId,
            'compartment_number' => $this->compartmentNumber,
            'volume' => $row['volume'],
            'dip_mm' => $row['dip_mm'],
            'captured_by' => auth()->id()
        ]);
    }
}
