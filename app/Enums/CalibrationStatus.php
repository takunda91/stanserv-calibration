<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Str;

enum CalibrationStatus: string implements HasColor, HasLabel
{
    case pending = 'pending';
    case in_progress = 'in_progress';
    case aborted = 'aborted';
    case completed = 'completed';

    public function color(): string
    {
        return match ($this) {
            self::pending => 'info',
            self::in_progress => 'warning',
            self::aborted => 'danger',
            self::completed => 'success',
        };
    }

    public function label(): string
    {
        return ucfirst(str_replace("_", " ", $this->value));
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::pending => 'info',
            self::in_progress => 'warning',
            self::aborted => 'danger',
            self::completed => 'success',
        };
    }

    public function getLabel(): string|Htmlable|null
    {
       return Str::of($this->value)->slug()->headline();
    }
}
