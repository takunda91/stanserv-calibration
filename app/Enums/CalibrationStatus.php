<?php

namespace App\Enums;

enum CalibrationStatus: string
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
}
