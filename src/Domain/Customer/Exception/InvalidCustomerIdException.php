<?php

declare(strict_types=1);

namespace Reservations\Domain\Customer\Exception;

use DomainException;

final class InvalidCustomerIdException extends DomainException
{
    public static function notAvailableUuid(string $given): self
    {
        return new self("Invalid customer ID: \"{$given}\" is not a valid UUID.");
    }
}
