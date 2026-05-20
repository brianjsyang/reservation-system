<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

/**
 * Represents the status of a reservation.
 * Add a small behaviour here:: isTermnial() and isActive()
 * Used by the Reservation to gate the transition of the reservation status.
 */
enum ReservationStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Seated     = 'seated';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';
    case NoShow     = 'no_show';

    /**
     * Terminal states cannot transition to anything else.
     * Terminal states: Completed, Cancelled, NoShow
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Cancelled, self::NoShow => true,
            default => false,
        };
    }

    /**
     * Active states: Pending, Confirmed, Seated
     * Naturally, Active states are NOT terminal states.
     */
    public function isActive(): bool
    {
        return !$this->isTerminal();
    }
}
