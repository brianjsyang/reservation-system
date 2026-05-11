<?php
// Why not use strict type in this file?

namespace Reservations\Domain\Shared\Exception;

use DomainException;

final class InvalidPartySizeException extends DomainException
{
    public static function tooSmall(int $given, int $min): self
    {
        return new self("Party size must be at least {$min}, got {$given}");
    }

    public static function tooLarge(int $given, int $max): self
    {
        return new self("Party size cannot exceed {$max}, got {$given}");
    }
}
