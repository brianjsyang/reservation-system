<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservation;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use Reservations\Domain\Reservation\Reservation;
use Reservations\Domain\Reservation\ReservationId;
use Reservations\Domain\Reservation\ReservationStatus;
use Reservations\Domain\Customer\CustomerId;
use Reservations\Domain\Shared\TimeSlot;
use Reservations\Domain\Shared\PartySize;


final class ReservationTest extends TestCase
{

    // Helpers reduce noise in every test
    private function reservationId(): ReservationId
    {
        return ReservationId::generate();
    }

    private function customerId(): CustomerId
    {
        return CustomerId::generate();
    }

    private function slot(): TimeSlot
    {
        return TimeSlot::of(new DateTimeImmutable('2026-05-12 19:00'), 90);
    }

    private function partySize(int $size = 4): PartySize
    {
        return new PartySize($size);
    }

    private function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('2026-05-10 10:00');
    }

    private function aPendingReservation(): Reservation
    {
        return Reservation::request(
            id: $this->reservationId(),
            customerId: $this->customerId(),
            slot: $this->slot(),
            partySize: $this->partySize(),
            now: $this->now(),
        );
    }

    // ─────────────────────────────────────────────────────────
    // Construction
    // ─────────────────────────────────────────────────────────

    public function test_request_starts_reservation_in_pending_status(): void
    {
        $reservation = $this->aPendingReservation();

        $this->assertSame(ReservationStatus::Pending, $reservation->status());
    }

    public function test_request_exposes_id_customer_slot_and_party_size(): void
    {
        $test_id = $this->reservationId();
        $test_customer_id = $this->customerId();
        $test_slot = $this->slot();
        $test_party_size = $this->partySize();

        $reservation = Reservation::request(
            id: $test_id,
            customerId: $test_customer_id,
            slot: $test_slot,
            partySize: $test_party_size,
            now: $this->now(),
        );

        $this->assertSame($test_id, $reservation->id());
        $this->assertSame($test_customer_id, $reservation->customerId());
        $this->assertSame($test_slot, $reservation->slot());
        $this->assertSame($test_party_size, $reservation->partySize());
    }

    public function test_confirm_transitions_pending_to_confirm(): void
    {
        // ARRANGE
        $reservation = $this->aPendingReservation();

        // ACT
        $reservation->confirm($this->now());

        // ASSERT
        $this->assertSame(ReservationStatus::Confirmed, $reservation->status());
    }

    /**
     * Test to write...
     * test_confirm_records_confirmation_time — drives the confirmedAt field
     * test_confirm_throws_when_already_confirmed — drives the state machine check
     * test_confirm_throws_when_cancelled — same guard, different path
     * test_cancel_transitions_pending_to_cancelled — drives cancel()
     * test_cancel_throws_when_already_cancelled
     * test_cancel_throws_when_seated
     */
}
