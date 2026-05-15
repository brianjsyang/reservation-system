<?php

declare(strict_types=1);

namespace Reservations\Tests\Unit\Domain\Reservation;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use Reservations\Domain\Reservation\Reservation;
use Reservations\Domain\Reservation\ReservationId;
use Reservations\Domain\Reservation\ReservationStatus;
use Reservations\Domain\Customer\CustomerId;
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
            customerId: new CustomerId('cust-1'),
            slot: TimeSlot::of(new DateTimeImmutable('2026-05-12 19:00'), 90),
            size: new PartySize(4),
            now: new DateTimeImmutable('2026-05-10 10:00'),
        );

        $this->assertSame(ReservationStatus::Pending, $reservation->status());
    }
}
