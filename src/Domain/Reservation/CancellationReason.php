<?php

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

/**
 * Week 1: enum with optional note.
 * A deliberate choice to not include accessor methods. No need for getters/setters.
 */
final class CancellationReason
{
    private function __construct(
        public readonly CancellationCategory $category,
        public readonly ?string $note,
    ) {}

    public static function customerRequested(?string $note = null): self
    {
        return new self(CancellationCategory::CustomerRequested, $note);
    }

    public static function restaurantClosed(?string $note = null): self
    {
        return new self(CancellationCategory::RestaurantClosed, $note);
    }

    public static function noShow(?string $note = null): self
    {
        return new self(CancellationCategory::NoShow, $note);
    }

    public static function systemCleanup(?string $note = null): self
    {
        return new self(CancellationCategory::SystemCleanup, $note);
    }
}

enum CancellationCategory: string
{
    case CustomerRequested = 'customer_requested';
    case RestaurantClosed  = 'restaurant_closed';
    case NoShow            = 'no_show';
    case SystemCleanup     = 'system_cleanup';
}
