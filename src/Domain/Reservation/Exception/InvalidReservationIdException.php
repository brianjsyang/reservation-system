<?php

namespace Reservations\Domain\Reservation\Exception;

use DomainException;

class InvalidReservationIdException extends DomainException
{
    public static function notAvailableUuid(): self
    {
        return new self('Invalid reservation ID: UUID is not available.');
    }
}
