<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

enum CancellationCategory: string
{
    case CustomerRequested = 'customer_requested';
    case RestaurantClosed  = 'restaurant_closed';
    case NoShow            = 'no_show';
    case SystemCleanup     = 'system_cleanup';
}
