<?php

namespace App\Models;

use App\Enums\CalibrateUsing;
use App\Enums\CalibrationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Calibration extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'status' => CalibrationStatus::class,
        'calibrate_using' => CalibrateUsing::class,
        'permit_to_work' => 'array',
        'risk_assessment' => 'array',
        'precheck' => 'array',
        'meta' => 'array'

    ];

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public static function generateCalibrationNumber(): string
    {
        $prefix = '83T';
        $year = date('Y');

        // Get next ID (if using auto-increment)
        $nextId = (static::max('id') ?? 0) + 1;

        // Pad with 3 digits — 001, 002, 010, etc.
        $sequence = str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }

    protected ?string $cachedTechniciansList = null;

    public function getTechniciansListAttribute(): string
    {
        if ($this->cachedTechniciansList !== null) {
            return $this->cachedTechniciansList;
        }

        $userIds = collect($this->meta['technicians'] ?? [])->flatten()->unique();

        $this->cachedTechniciansList = $userIds->map(fn ($id) => User::find($id)?->name)
            ->filter()
            ->implode(' | ');

        return $this->cachedTechniciansList;
    }

    public function readings(): Calibration|HasMany
    {
        return $this->hasMany(CalibrationReading::class);
    }

    public function compartments(): Calibration|HasMany
    {
        return $this->hasMany(CalibrationCompartment::class);
    }

}
