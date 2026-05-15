<?php

namespace Reservations\Domain\Customer\Exception;

use DomainException;

class InvalidCustomerIdException extends DomainException
{
    public static function notAvailableUuid(): self
    {
        return new self('Invalid customer ID: UUID is not available.');
    }
}
