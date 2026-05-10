<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Reservations\Domain\Shared\TimeSlot;

final class TimeSlotTest extends TestCase
{
    public static function invalidDurations(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
        ];
    }

    #[DataProvider('invalidDurations')]
    public function test_reject_invalid_duration(int $durationInMinutes): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TimeSlot(new DateTimeImmutable('7:00pm'), $durationInMinutes);
    }

    public function test_overlapping_slots(): void
    {
        $a = new TimeSlot(new DateTimeImmutable('7:00pm'), 90); // 7:00pm - 8:30pm
        $b = new TimeSlot(new DateTimeImmutable('8:00pm'), 90); // 8:00pm - 9:30pm

        $this->assertTrue($a->overlaps($b));
    }

    public function test_adjacent_slots_do_not_overlap(): void
    {
        $a = new TimeSlot(new DateTimeImmutable('7:00pm'), 60); // 7:00pm - 8:00pm
        $b = new TimeSlot(new DateTimeImmutable('8:00pm'), 60); // 8:00pm - 9:00pm

        $this->assertFalse($a->overlaps($b));
    }

    public function test_non_overlapping_slots(): void
    {
        $a = new TimeSlot(new DateTimeImmutable('7:00pm'), 60); // 7:00pm - 8:00pm
        $b = new TimeSlot(new DateTimeImmutable('9:00pm'), 60); // 9:00pm - 10:00pm

        $this->assertFalse($a->overlaps($b));
    }
}
