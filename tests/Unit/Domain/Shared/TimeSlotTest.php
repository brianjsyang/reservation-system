<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Reservations\Domain\Shared\Exception\InvalidTimeSlotException;
use Reservations\Domain\Shared\TimeSlot;

final class TimeSlotTest extends TestCase
{
    /**
     * Helper function for the Test
     * Manually set a date to avoid timezone/date ambiguity.
     */
    private function at(string $time): DateTimeImmutable
    {
        return new DateTimeImmutable("2026-05-12 {$time}");
    }

    // --- Construction & invariants -----------------------------------------

    public static function invalidDurations(): array
    {
        return [
            'just below minimum' => [TimeSlot::MIN_DURATION_MINUTES - 1],
            'just above maximum' => [TimeSlot::MAX_DURATION_MINUTES + 1],
            'very negative'      => [-1000],
        ];
    }

    #[DataProvider('invalidDurations')]
    public function test_of_rejects_invalid_duration(int $duration): void
    {
        $this->expectException(InvalidTimeSlotException::class);
        TimeSlot::of($this->at('19:00'), $duration);
    }

    public function test_of_accepts_minimum_duration(): void
    {
        $slot = TimeSlot::of($this->at('19:00'), TimeSlot::MIN_DURATION_MINUTES);
        $this->assertSame(TimeSlot::MIN_DURATION_MINUTES, $slot->durationInMinutes());
    }

    public function test_of_accepts_maximum_duration(): void
    {
        $slot = TimeSlot::of($this->at('19:00'), TimeSlot::MAX_DURATION_MINUTES);
        $this->assertSame(TimeSlot::MAX_DURATION_MINUTES, $slot->durationInMinutes());
    }

    public function test_between_rejects_end_before_start(): void
    {
        $this->expectException(InvalidTimeSlotException::class);
        TimeSlot::between($this->at('19:00'), $this->at('18:00'));
    }

    public function test_between_rejects_end_equal_to_start(): void
    {
        $this->expectException(InvalidTimeSlotException::class);
        TimeSlot::between($this->at('19:00'), $this->at('19:00'));
    }

    // --- Overlap (with symmetry checks) ------------------------------------

    public function test_slots_fully_separated_do_not_overlap(): void
    {
        $a = TimeSlot::of($this->at('19:00'), 60);
        $b = TimeSlot::of($this->at('15:00'), 60);
        $this->assertFalse($a->overlaps($b));
        $this->assertFalse($b->overlaps($a));
    }

    public function test_slots_adjacent_do_not_overlap(): void
    {
        $a = TimeSlot::of($this->at('19:00'), 60);
        $b = TimeSlot::between($this->at('15:00'), $this->at('19:00'));
        $this->assertFalse($a->overlaps($b));   // tests $a->startsAt'19:00' with $b->endsAt'19:00'
        $this->assertFalse($b->overlaps($a));   // tests $b->endsAt'19:00' with $a->startsAt'19:00'
    }

    public function test_slots_partial_separated_overlap(): void
    {
        $a = TimeSlot::between($this->at('19:00'), $this->at('20:00'));
        $b = TimeSlot::between($this->at('18:30'), $this->at('19:30'));
        $this->assertTrue($a->overlaps($b));
        $this->assertTrue($b->overlaps($a));
    }

    public function test_slots_contained_overlap(): void
    {
        $a = TimeSlot::between($this->at('14:00'), $this->at('20:00'));
        $b = TimeSlot::between($this->at('16:00'), $this->at('17:00'));
        $this->assertTrue($a->overlaps($b));
    }

    // --- Equals (with symmetry checks) ------------------------------------

    public function test_slots_same_time_and_duration_equal(): void
    {
        $a = TimeSlot::of($this->at('14:00'), 60);
        $b = TimeSlot::of($this->at('14:00'), 60);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    public function test_slots_same_time_and_between_equal(): void
    {
        $a = TimeSlot::between($this->at('14:00'), $this->at('16:00'));
        $b = TimeSlot::between($this->at('14:00'), $this->at('16:00'));
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    public function test_slots_same_of_and_between_equal(): void
    {
        $a = TimeSlot::between($this->at('14:00'), $this->at('16:00'));
        $b = TimeSlot::of($this->at('14:00'), 120);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));
    }

    public function test_slots_same_times_diff_duration_not_equal(): void
    {
        $a = TimeSlot::of($this->at('14:00'), 60);
        $b = TimeSlot::of($this->at('14:00'), 90);
        $this->assertFalse($a->equals($b));
        $this->assertFalse($b->equals($a));
    }

    public function test_slots_same_times_diff_between_not_equal(): void
    {
        $a = TimeSlot::between($this->at('14:00'), $this->at('15:00'));
        $b = TimeSlot::between($this->at('14:00'), $this->at('16:00'));
        $this->assertFalse($a->equals($b));
        $this->assertFalse($b->equals($a));
    }
}
