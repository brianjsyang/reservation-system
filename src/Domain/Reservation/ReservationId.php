<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

use Ramsey\Uuid\Uuid;
// use Ramsey\Uuid\UuidInterface; // for now, will store ID as string, not UuidInterface
use Reservations\Domain\Reservation\Exception\InvalidReservationIdException;

final class ReservationId
{
    private function __construct(private readonly string $value) {}

    public static function generate(): self
    {
        // create a fresh UUID
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        // Rehydrate from DB/input
        if (!Uuid::isValid($value)) {
            throw InvalidReservationIdException::notAvailableUuid($value);
        }
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
