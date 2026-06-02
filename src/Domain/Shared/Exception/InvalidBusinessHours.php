<?php

declare(strict_types=1);

namespace Reservations\Domain\Shared\Exception;

use DomainException;

final class InvalidBusinessHours extends DomainException
{

    public static function openAfterClose(int $givenOpen, int $givenClose): self
    {
        return new self("Invalid Store Houres: Open Time is AFTER Close Time. Open: {$givenOpen}, Close: {$givenClose}");
    }

    public static function dayOfWeekEmpty(): self
    {
        return new self("Invalid Day of Week: dayOfWeek is empty");
    }

    public static function dayOfWeekOutOfScope(int $given): self
    {
        return new self("Invalid Day of Week: dayOfWeek must be between 1..7. Given {$given}");
    }
}
