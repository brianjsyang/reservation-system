<?php

declare(strict_types=1);
/**
 * PartySize will be set up as value object
 * No need for identity
 */
final class PartySize
{
    public function __construct(private readonly int $partySize)
    {
        if ($partySize < 1 || $partySize > 15) {
            throw new \InvalidArgumentException("Party size must be larger than 1 and less than 15. Inserted {$partySize}");
        }
    }

    public function value(): int
    {
        return $this->partySize;
    }

    /**
     * Equality comparison
     */
    public function equals(self $other): bool
    {
        return $this->partySize === $other->partySize;
    }
}
