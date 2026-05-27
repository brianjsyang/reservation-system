<?php
// TODO: Complete later!
declare(strict_types=1);

namespace Reservations\Domain\Customer;

use Ramsey\Uuid\Uuid;
// use Ramsey\Uuid\UuidInterface; // for now, will store ID as string, not UuidInterface
use Reservations\Domain\Customer\Exception\InvalidCustomerIdException;

final class CustomerId
{
    private function __construct(private readonly string $value) {}

    public static function generate(): self
    {
        // Generate a fresh UUID for a new CustomerId
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        // Rehydrate from DB/input
        if (!Uuid::isValid($value)) {
            throw InvalidCustomerIdException::notAvailableUuid($value);
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
