<?php

declare(strict_types=1);

namespace Reservations\Domain\Shared;

use InvalidArgumentException;
use Reservations\Domain\Shared\Exception\InvalidPartySizeException;

/**
 * PartySize will be set up as value object
 * No need for identity
 */
final class PartySize
{
    public const MIN = 1;
    public const MAX = 15;

    public function __construct(private readonly int $size)
    {
        // Use custom domain specific exceptions
        if ($size < self::MIN) {
            throw InvalidPartySizeException::tooSmall($size, self::MIN);
        }
        if ($size > self::MAX) {
            throw InvalidPartySizeException::tooLarge($size, self::MAX);
        }
    }

    public function size(): int
    {
        return $this->size;
    }

    /**
     * Equality comparison
     */
    public function equals(self $other): bool
    {
        return $this->size === $other->size;
    }

    /**
     * Comparison
     */
    public function exceeds(self $other): bool
    {
        return $this->size > $other->size;
    }
}
