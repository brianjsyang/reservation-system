<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

enum ReservationStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Seated    = 'seated';
    case Completed = 'completed';
    case NoShow    = 'no_show';
    case Cancelled = 'cancelled';
}
