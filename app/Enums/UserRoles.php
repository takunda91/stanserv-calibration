<?php

namespace App\Enums;

enum UserRoles: string
{
    case TECHNICIAN = 'technician';
    case SUPERVISOR = 'supervisor';
    case ADMIN = 'admin';

    public function color(): string
    {
        return match ($this) {
            self::TECHNICIAN => 'warning',
            self::SUPERVISOR => 'default',
            self::ADMIN => 'info',
        };
    }

    public static function getRoleEnum(string $role): ?UserRoles
    {
        foreach (self::cases() as $key => $value) {
            if ($value->value === $role) {
                return $value;
            }
        }
        return null;
    }

}
