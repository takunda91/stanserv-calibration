<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CalibrationCertificateController;

Route::get('/', function () {
    return redirect()->to('admin');
});

Route::get('/calibrations/{calibration}/certificate', CalibrationCertificateController::class)
    ->name('calibrations.certificate');
