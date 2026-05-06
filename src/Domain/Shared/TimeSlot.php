<?php

declare(strict_types=1);
/**
 * Time Slot will be set up as value object
 * Any TimeSlot set at "7pm" is the same.
 * No need for identity
 */

final class TimeSlot
{
    public function __construct(private readonly DateTimeImmutable $start, private readonly int $durationInMinutes)
    {
        if (!$start || $durationInMinutes < 0) {
            throw new \InvalidArgumentException('Either Start is empty or Duration is not a valid input');
        }
    }
}
