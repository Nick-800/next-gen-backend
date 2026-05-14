<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Active   = 'active';
    case Draft    = 'draft';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Draft    => 'Draft',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active   => 'success',
            self::Draft    => 'warning',
            self::Archived => 'gray',
        };
    }
}
