<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalibrationReadingInterpolation extends Model
{
    public function reading(): BelongsTo
    {
        return $this->belongsTo(CalibrationReading::class, 'calibration_reading_id');
    }

    public function runBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'run_by');
    }
}
