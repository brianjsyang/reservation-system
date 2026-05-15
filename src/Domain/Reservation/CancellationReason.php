<?php
// TODO: Expand to using enum-with-optional-note
// to allow more detailed reason
declare(strict_types=1);

namespace Reservations\Domain\Reservation;

enum CancellationReason: string
{
    case CustomerRequest   = 'customer_request';
    case DoubleBooked      = 'double_booked';
    case StaffRequest      = 'staff_request';
    case RestaurantClosed  = 'restaurant_closed';
    case CapacityReduced   = 'capacity_reduced';
    case SystemCleanup     = 'system_cleanup';
    case Expired           = 'expired';
}
