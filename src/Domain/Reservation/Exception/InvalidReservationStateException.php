<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation\Exception;

use DomainException;
use Reservations\Domain\Reservation\ReservationStatus;

final class InvalidReservationStateException extends DomainException
{
    public static function cannotConfirmFrom(ReservationStatus $current): self
    {
        return new self("Cannot confirm a reservation in status '{$current->value}'.");
    }

    public static function cannotCancelFrom(ReservationStatus $current): self
    {
        return new self("Cannot cancel a reservation in status '{$current->value}'.");
    }

    public static function cannotSeatFrom(ReservationStatus $current): self
    {
        return new self("Cannot seat a reservation in status '{$current->value}'.");
    }

    public static function cannotCompleteFrom(ReservationStatus $current): self
    {
        return new self("Cannot complete a reservation in status '{$current->value}'.");
    }

    public static function cannotMarkNoShowFrom(ReservationStatus $current): self
    {
        return new self("Cannot mark no-show for a reservation in status '{$current->value}'.");
    }

    public static function cannotRescheduleFrom(ReservationStatus $current): self
    {
        return new self("Cannot reschedule a reservation in status '{$current->value}'.");
    }
}
