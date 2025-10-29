<?php

namespace App\Models;

use App\Enums\AirBagsConfig;
use App\Enums\CalibrationStatus;
use App\Enums\TankShapes;
use App\Enums\TruckSuspensionTypes;
use App\Enums\TruckTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Truck extends Model
{
    protected $guarded = [];
    protected $casts = [
        'truck_type' => TruckTypes::class,
        'tank_shape' => TankShapes::class,
        'truck_suspension_type' => TruckSuspensionTypes::class,
        'air_bags' => AirBagsConfig::class,
    ];

    public function getRegInfoAttribute(): string
    {
        return "$this->horse_reg | $this->trailer_reg" ;
    }

    public function calibrations(): Truck|HasMany
    {
        return $this->hasMany(Calibration::class);
    }

    public function inCalibration(): void
    {
        $this->calibrations()->where('status', CalibrationStatus::in_progress)->exists();
    }

    // Truck.php
    public function scopeNotInCalibration(Builder $query): Builder
    {
        return $query->whereDoesntHave('calibrations', function ($q) {
            $q->where('status', CalibrationStatus::in_progress);
        });
    }
}
