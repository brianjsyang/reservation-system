<?php
// Why not use strict type in this file?

namespace Reservations\Domain\Shared\Exception;

use DomainException;

final class InvalidTimeSlotException extends DomainException
{
    public static function tooShort(int $given, int $min): self
    {
        return new self("Duration must be at least {$min}, got {$given}");
    }

    public static function tooLong(int $given, int $max): self
    {
        return new self("Duration cannot exceed {$max}, got {$given}");
    }

    public static function endsBeforeStart(): self
    {
        return new self("End time is less than or equal to start time");
    }

    // TODO: Complete function
    public static function missingTimeZone(): self
    {
        return new self("Missing time zone");
    }
}
