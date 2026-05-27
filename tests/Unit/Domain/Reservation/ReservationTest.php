<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservation;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use Reservations\Domain\Reservation\Reservation;
use Reservations\Domain\Reservation\ReservationId;
use Reservations\Domain\Reservation\ReservationStatus;
use Reservations\Domain\Customer\CustomerId;
use Reservations\Domain\Reservation\CancellationCategory;
use Reservations\Domain\Reservation\CancellationReason;
use Reservations\Domain\Shared\TimeSlot;
use Reservations\Domain\Shared\PartySize;

use Reservations\Domain\Reservation\Exception\InvalidReservationIdException;

final class ReservationTest extends TestCase
{

    // --- Construction & invariants -----------------------------------------

    public function test_generate_produces_a_valid_uuid_string(): void
    {
        $id = ReservationId::generate();
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $id->value(),
        );
    }

    public function test_two_generated_ids_are_not_equal(): void
    {
        $this->assertFalse(ReservationId::generate()->equals(ReservationId::generate()));
    }

    public function test_two_ids_from_same_string_are_equal(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $this->assertTrue(
            ReservationId::fromString($uuid)->equals(ReservationId::fromString($uuid)),
        );
    }

    public function test_from_string_rejects_invalid_uuid(): void
    {
        $this->expectException(InvalidReservationIdException::class);
        ReservationId::fromString('not-a-uuid');
    }

    public function test_requesting_a_reservation_starts_it_in_pending_status(): void
    {
        $reservation = Reservation::request(
            id: ReservationId::generate(),
            customerId: CustomerId::generate(),
            slot: TimeSlot::of(new DateTimeImmutable('2026-05-12 19:00'), 90),
            partySize: new PartySize(4),
            now: new DateTimeImmutable('2026-05-10 10:00'),
        );

        $this->assertSame(ReservationStatus::Pending, $reservation->status());
    }

    // --- Reservation status transitions -----------------------------------------

    public function test_terminal_states_are_identified_correctly(): void
    {
        $this->assertTrue(ReservationStatus::Completed->isTerminal());
        $this->assertTrue(ReservationStatus::Cancelled->isTerminal());
        $this->assertTrue(ReservationStatus::NoShow->isTerminal());

        $this->assertFalse(ReservationStatus::Pending->isTerminal());
        $this->assertFalse(ReservationStatus::Confirmed->isTerminal());
        $this->assertFalse(ReservationStatus::Seated->isTerminal());
    }


    // --- Cancellation Reasons -----------------------------------------

    public function test_customer_requested_creates_correct_category(): void
    {
        $reason = CancellationReason::customerRequested('kid sick');
        $this->assertSame(CancellationCategory::CustomerRequested, $reason->category);
        $this->assertSame('kid sick', $reason->note);
    }

    public function test_note_is_optional(): void
    {
        $reason = CancellationReason::customerRequested();
        $this->assertNull($reason->note);
    }


    // -----------------------------------------
    // --- Reservation Test --------------------
    // -----------------------------------------
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
        // Worth noting: previous test already confirms that all newly created reservations start in pending status
        $this->assertSame(ReservationStatus::Confirmed, $reservation->status());
    }

    /**
     * Test to write...
     * test_confirm_records_confirmation_time — drives the confirmedAt field
     * test_confirm_throws_when_already_confirmed — drives the state machine check
     * test_confirm_throws_when_cancelled — same guard, different path
     * test_cancel_transitions_pending_to_cancelled — drives cancel()
     * test_cancel_records_reason_and_time
     * test_cancel_throws_when_already_cancelled
     * test_cancel_throws_when_seated
     */
}
