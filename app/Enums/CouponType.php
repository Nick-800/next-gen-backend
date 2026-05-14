<?php

namespace App\Enums;

enum CouponType: string
{
    case Percentage = 'percentage';
    case Fixed      = 'fixed';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Percentage (%)',
            self::Fixed      => 'Fixed Amount ($)',
        };
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculate(float $value, float $subtotal): float
    {
        return match ($this) {
            self::Percentage => round($subtotal * ($value / 100), 2),
            self::Fixed      => min($value, $subtotal),
        };
    }
}
