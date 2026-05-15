<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

use Ramsey\Uuid\Uuid;
// use Ramsey\Uuid\UuidInterface; // for now, will store ID as string, not UuidInterface
use Reservations\Domain\Reservation\Exception\InvalidReservationIdException;

final class ReservationId
{
    public function __construct(private readonly string $value) {}

    // Named constructor to generate a new UUID-based ReservationId
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    // Named constructor to create a ReservationId from a string
    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw InvalidReservationIdException::notAvailableUuid();
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
