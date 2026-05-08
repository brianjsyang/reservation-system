<?php

declare(strict_types=1);

namespace Reservations\Domain\Shared;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Time Slot will be set up as value object
 * Any TimeSlot set at "7pm" is the same.
 * No need for identity
 */

final class TimeSlot
{
    private readonly DateTimeImmutable $endsAt;

    public function __construct(
        private readonly DateTimeImmutable $startsAt,
        private readonly int $durationInMinutes
    ) {
        if ($durationInMinutes < 1) {
            throw new InvalidArgumentException('Duration must be at least 1 minutes');
        }

        $this->endsAt = $startsAt->modify("+{$durationInMinutes} minutes");
    }

    public function startsAt(): DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function endsAt(): DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function durationInMinutes(): int
    {
        return $this->durationInMinutes;
    }
}
