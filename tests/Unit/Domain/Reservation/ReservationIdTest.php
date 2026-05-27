<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservation;

use PHPUnit\Framework\TestCase;
use Reservations\Domain\Reservation\Exception\InvalidReservationIdException;
use Reservations\Domain\Reservation\ReservationId;

final class ReservationIdTest extends TestCase
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
        $this->expectExceptionMessage('not-a-uuid');
        ReservationId::fromString('not-a-uuid');
    }
}
