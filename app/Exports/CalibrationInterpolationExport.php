<?php

namespace App\Exports;

use App\Models\Calibration;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CalibrationInterpolationExport implements FromArray, WithStyles
{

    public function __construct(public Calibration $calibration){

    }

    public function array(): array
    {
        $rows = [];

        // Row 1 — Title
        $rows[] = ["CALIBRATION REPORT - {$this->calibration->truck->horse_reg} - {$this->calibration->created_at->format('Y-m-d')}"];

        // Row 2 — blank
        $rows[] = [];

        // Fetch compartments
        $compartments = $this->calibration->compartments;
        $compCount = $compartments->count();

        // Row 3 — Compartment labels
        $row3 = [];
        foreach ($compartments as $comp) {
            $row3[] = "Compartment {$comp->number}";
            $row3[] = null;
            $row3[] = null;
        }
        $rows[] = $row3;

        // Row 4 — Column Headers
        $row4 = [];
        foreach ($compartments as $comp) {
            $row4[] = "Dip (mm)";
            $row4[] = "Volume (L)";
            $row4[] = "Average";
        }
        $rows[] = $row4;

        // Now the data rows
        $maxRows = $this->calibration->interpolations->groupBy('compartment_number')->map->count()->max();

        for ($i = 0; $i < $maxRows; $i++) {
            $row = [];

            foreach ($compartments as $comp) {
                $reading = $this->calibration->interpolations
                    ->where('compartment_number', $comp->number)
                    ->values()
                    ->get($i);

                $row[] = $reading->dip_mm ?? null;
                $row[] = $reading->volume ?? null;
                $row[] = $reading->interpolation_average ?? null;
            }

            $rows[] = $row;

        }

        return $rows;
    }

    /**
     * @throws Exception
     */
    public function styles(Worksheet $sheet): void
    {
        $compCount = $this->calibration->compartments->count();

        for ($i = 0; $i < $compCount; $i++) {

            $startColIndex = $i * 3 + 1;
            $endColIndex   = $startColIndex + 2;

            // Convert to A1 column letters
            $startCol = Coordinate::stringFromColumnIndex($startColIndex);
            $endCol   = Coordinate::stringFromColumnIndex($endColIndex);

            // Merge compartment header (Row 3)
            $range = "{$startCol}2:{$endCol}2";
            $sheet->mergeCells($range);

            // Center align
            $sheet->getStyle($range)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($range)->getAlignment()->setVertical('center');

            $sheet->getStyle("{$startCol}2:{$endCol}3")->getFont()->setBold(true);

            $sheet->getStyle("{$endCol}3:{$endCol}{$sheet->getHighestRow()}")
                ->getBorders()
                ->getRight()
                ->setBorderStyle(Border::BORDER_MEDIUM);
        }

        // Merge report title row (Row 1)
        $firstCol = 'A';
        $lastCol  = Coordinate::stringFromColumnIndex($compCount * 3);
        $titleRange = "A1:{$lastCol}1";

        $sheet->mergeCells($titleRange);
        $sheet->getStyle($titleRange)->getAlignment()->setHorizontal('center');
    }

}
