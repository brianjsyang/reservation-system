<?php

declare(strict_types=1);

namespace Reservations\Domain\Shared;

use BusinessHours;
use DateTimeImmutable;
use Reservations\Domain\Shared\Exception\InvalidTimeSlotException;

final class TimeSlot
{
    public const MIN_DURATION_MINUTES = 1;
    public const MAX_DURATION_MINUTES = 480;

    public function __construct(
        private readonly DateTimeImmutable $startsAt,
        private readonly DateTimeImmutable $endsAt,
        private readonly int $durationInMinutes
    ) {}

    // Named Constructor Pattern - fake overloading with static factories.
    // Reads like TimeSlot::of($sixPm, 60) // 6:00pm - 7:00pm
    public function of(DateTimeImmutable $startsAt, int $durationInMinutes): self
    {
        if ($durationInMinutes < self::MIN_DURATION_MINUTES) {
            throw InvalidTimeSlotException::tooShort($durationInMinutes, self::MIN_DURATION_MINUTES);
        }
        if ($durationInMinutes > self::MAX_DURATION_MINUTES) {
            throw InvalidTimeSlotException::tooLong($durationInMinutes, self::MAX_DURATION_MINUTES);
        }
        return new self(
            $startsAt,
            $startsAt->modify("+{$durationInMinutes} minutes"),
            $durationInMinutes,
        );
    }

    // Reads like TimeSlot::between($sixPm, $sevenPm) // 6:00pm - 7:00pm
    public function between(DateTimeImmutable $startsAt, DateTimeImmutable $endsAt): self
    {
        if ($endsAt <= $startsAt) {
            throw InvalidTimeSlotException::endsBeforeStart();
        }
        $duration = (int) (($endsAt->getTimestamp() - $startsAt->getTimestamp()) / 60);
        return new self($startsAt, $endsAt, $duration);
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

    public function equals(self $other): bool
    {
        // Key knowledge: For DateTimeImmutable, == will check VALUE; === will check OBJECT IDENTITY (same instance)
        return $this->startsAt == $other->startsAt && $this->durationInMinutes && $other->durationInMinutes;
    }


    /**
     * Two TimeSlots are considred "overlapped" if:
     * 1. $this->startsAt is LESS than $other->endsAt ($this starts before $other ends)
     * 2. $this->endsAt is GREATER than $other->startsAt ($this ends after $other starts)
     * 3. Adjacent slots (A ends exactly when B starts) is allowed (not an overlap)
     */
    public function overlaps(self $other): bool
    {
        return $this->startsAt < $other->endsAt && $this->endsAt > $other->startsAt;
    }


    /**
     * TimeSlot asks BusinessHours to check itself. (Tell-Don't-Ask)
     * BusinessHours -knows- what "within" means, TimeSlot doesn't have to
     */
    public function isWithin(BusinessHours $hours): bool
    {
        return $hours->contains($this);
    }
}
