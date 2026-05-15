<?php
// TODO: Complete later!
declare(strict_types=1);

namespace Reservations\Domain\Customer;

use Ramsey\Uuid\Uuid;
// use Ramsey\Uuid\UuidInterface; // for now, will store ID as string, not UuidInterface
use Reservations\Domain\Customer\Exception\InvalidCustomerIdException;

final class CustomerId
{
    public function __construct(private readonly string $value) {}

    // Named constructor to generate a new UUID-based ReservationId
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    // Named constructor to create a ReservationId from a string
    // Used for rehydrating from a string value (e.g., from a database)
    public static function fromString(string $value): self
    {
        if (!Uuid::isValid($value)) {
            throw InvalidCustomerIdException::notAvailableUuid();
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
