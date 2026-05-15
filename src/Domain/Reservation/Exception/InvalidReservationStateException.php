<?php

namespace Reservations\Domain\Reservation\Exception;

use DomainException;

class InvalidReservationStateException extends DomainException
{
    public static function cannotCancelForm(): self
    {
        return new self('Cannot cancel a reservation that has already been cancelled.');
    }

    public static function cannotConfirmForm(): self
    {
        return new self('Cannot confirm a reservation that has already been confirmed.');
    }
}
