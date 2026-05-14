<?php

namespace App\Enums;

enum ProductType: string
{
    case Physical = 'physical';
    case Service  = 'service';

    public function label(): string
    {
        return match ($this) {
            self::Physical => 'Physical Product',
            self::Service  => 'Service / Warranty',
        };
    }

    /**
     * Service items require no shipping calculation.
     */
    public function requiresShipping(): bool
    {
        return $this === self::Physical;
    }
}
