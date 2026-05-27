<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation\Exception;

use DomainException;

final class InvalidReservationIdException extends DomainException
{
    public static function notAvailableUuid(string $given): self
    {
        return new self("Invalid reservation ID: \"{$given}\" is not a valid UUID.");
    }
}
