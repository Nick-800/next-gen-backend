<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft      = 'draft';
    case Pending    = 'pending';
    case Processing = 'processing';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    /**
     * Defines allowed state transitions.
     * Enforced by TransitionOrderStatusAction.
     */
    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft      => in_array($next, [self::Pending, self::Cancelled]),
            self::Pending    => in_array($next, [self::Processing, self::Cancelled]),
            self::Processing => in_array($next, [self::Completed, self::Cancelled]),
            self::Completed  => false,
            self::Cancelled  => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Draft      => 'Draft (Quote)',
            self::Pending    => 'Pending',
            self::Processing => 'Processing',
            self::Completed  => 'Completed',
            self::Cancelled  => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft      => 'gray',
            self::Pending    => 'warning',
            self::Processing => 'info',
            self::Completed  => 'success',
            self::Cancelled  => 'danger',
        };
    }
}
