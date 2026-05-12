<?php

declare(strict_types=1);

use Reservations\Domain\Shared\TimeSlot;

final class BusinessHours
{
    /**
     * @param array<int> $daysOfWeek    1=Monday ... 7=Sunday (ISO-8601)
     */
    public function __construct(
        private readonly int $opensAtMinuteOfDay,
        private readonly int $closesAtMinuteOfDay,
        private readonly array $daysOfWeek,
    ) {
        // TODO: Complete the class...
    }


    // TODO: Complete this function
    public function contains(TimeSlot $time)
    {
        return false;
    }
}
