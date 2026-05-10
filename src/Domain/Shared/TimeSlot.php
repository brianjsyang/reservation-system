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


    /**
     * Two TimeSlots are considred "overlapped" if:
     * 1. $this->startsAt is LESS than $other->endsAt ($this starts before $other ends)
     * 2. $this->endsAt is GREATER than $other->startsAt ($this ends after $other starts)
     * 3. Adjacent slots (A ends exactly when B starts) is allowed (not an overlap)
     */
    public function overlaps(TimeSlot $other): bool
    {
        return $this->startsAt < $other->endsAt() && $this->endsAt > $other->startsAt();
    }
}
