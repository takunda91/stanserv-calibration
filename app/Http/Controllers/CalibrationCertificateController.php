<?php

namespace App\Http\Controllers;

use App\Models\Calibration;
use Illuminate\Http\Request;

class CalibrationCertificateController extends Controller
{
    public function __invoke(Calibration $calibration)
    {
        $calibration->load(['truck', 'compartments', 'interpolations', 'readings']);

        return view('calibrations.certificate', [
            'calibration' => $calibration,
            'truck' => $calibration->truck,
        ]);
    }
}
