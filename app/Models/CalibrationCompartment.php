<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalibrationCompartment extends Model
{
    protected $guarded = [];

    public function calibration(): BelongsTo
    {
        return $this->belongsTo(Calibration::class);
    }

    public function readings(): HasMany|CalibrationCompartment
    {
       return $this->hasMany(CalibrationReading::class, 'compartment_number', 'number')
           ->where('calibration_id', $this->calibration_id);
    }
}
