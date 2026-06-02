<?php

declare(strict_types=1);

namespace Reservations\Domain\Shared;

use DateTimeImmutable;
use Reservations\Domain\Shared\Exception\InvalidBusinessHours;

/**
 * Decision: business hours are constrained to a single calendar day.
 * opensAt < closesAt is enforced, and slots crossing midnight are rejected.
 * Overnight hours (e.g., 18:00–02:00) are intentionally not supported.
 */
final class BusinessHours
{
    /**
     * @param array<int> $daysOfWeek 1=Monday ... 7=Sunday (ISO-8601)
     */
    public function __construct(
        private readonly int $opensAtMinuteOfDay,   // e.g., 11*60 = 11:00
        private readonly int $closesAtMinuteOfDay,  // e.g., 22*60 = 22:00
        private readonly array $daysOfWeek,
    ) {
        // invariant checks: opens < closes, days non-empty, all 1..7, etc.
        if ($opensAtMinuteOfDay >= $closesAtMinuteOfDay) {
            throw InvalidBusinessHours::openAfterClose($opensAtMinuteOfDay, $closesAtMinuteOfDay);
        }

        if ($daysOfWeek === []) {
            throw InvalidBusinessHours::dayOfWeekEmpty();
        }

        foreach ($daysOfWeek as $d) {
            if ($d < 1 || $d > 7) {
                throw InvalidBusinessHours::dayOfWeekOutOfScope($d);
            }
        }
    }

    // Check whether given TimeSlot falls entirely within the BusinessHours.
    public function contains(TimeSlot $slot): bool
    {
        $start = $slot->startsAt();
        $end   = $slot->endsAt();

        // Invariant: same calendar day (decision documented on the class)
        if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
            return false;
        }

        // Convert to int
        $dow              = (int) $start->format('N');         // 1..7
        $startMinuteOfDay = (int) $start->format('G') * 60 + (int) $start->format('i');
        $endMinuteOfDay   = (int) $end->format('G')   * 60 + (int) $end->format('i');

        // comparison logic
        // 1. start's day-of-week is in $daysOfWeek
        // 2. start's minute-of-day >= opensAt
        // 3. end's minute-of-day <= closesAt
        // 4. start and end fall on the same day (decide consciously)
        return true;
    }
}
