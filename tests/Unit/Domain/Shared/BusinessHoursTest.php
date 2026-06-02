<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Reservations\Domain\Shared\BusinessHours;
use Reservations\Domain\Shared\TimeSlot;

final class BusinessHoursTest extends TestCase
{
    // --- Help Functions -----------------------------------------

    private function mondayMorningHour(): TimeSlot
    {
        return TimeSlot::of(new DateTimeImmutable('2026-06-01 09:00:00'), 60);
    }

    private function saturdayMorningHour(): TimeSlot
    {
        return TimeSlot::of(new DateTimeImmutable('2026-06-06 09:00:00'), 60);
    }

    private function mondayPreOpenHour(): TimeSlot
    {
        return TimeSlot::of(new DateTimeImmutable('2026-06-01 06:00:00'), 60);
    }

    private function mondayClosingEdge(): TimeSlot
    {
        return TimeSlot::of(new DateTimeImmutable('2026-06-01 17:00:00'), 60);
    }

    // --- Construction & invariants -----------------------------------------

    public function test_it_creates_a_valid_business_hour()
    {
        $a = new BusinessHours(7 * 60, 18 * 60, [1, 2, 3, 4, 5]);
        $b = new BusinessHours(6 * 60, 16 * 60, [3, 4, 5, 6, 7]);

        $this->assertEquals($a, $b);    // passes - both BusinessHours
        $this->assertNotSame($a, $b);  // passes - different instances
    }

    public static function invalidBusinessHours(): array
    {
        return [
            'openAfterClose' => '',
        ];
    }
}
