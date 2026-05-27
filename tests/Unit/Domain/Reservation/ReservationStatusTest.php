<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservation;

use PHPUnit\Framework\TestCase;
use Reservations\Domain\Reservation\ReservationStatus;

final class ReservationStatusTest extends TestCase
{
    // --- Reservation status transitions -----------------------------------------

    public function test_terminal_states_are_identified_correctly(): void
    {
        $this->assertTrue(ReservationStatus::Completed->isTerminal());
        $this->assertTrue(ReservationStatus::Cancelled->isTerminal());
        $this->assertTrue(ReservationStatus::NoShow->isTerminal());
    }

    public function test_non_terminal_states_are_identified_correctly(): void
    {
        $this->assertFalse(ReservationStatus::Pending->isTerminal());
        $this->assertFalse(ReservationStatus::Confirmed->isTerminal());
        $this->assertFalse(ReservationStatus::Seated->isTerminal());
    }
}
