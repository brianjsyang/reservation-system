<?php

/**
 *
 * Aggregate root for everything reservation-related.
 * External code talks to Reservation; it never reaches around the back to mutate a TimeSlot or change a status directly.
 *
 */

declare(strict_types=1);

use DateTimeImmutable;
use Reservations\Domain\Shared\PartySize;
use Reservations\Domain\Shared\TimeSlot;

final class Reservation
{
    // ──────────────────────────────────────────────────────────────
    // Construction — only via named factory, never `new` from outside
    // ──────────────────────────────────────────────────────────────
    private function __construct(...) {}

    public static function request(
        ReservationId $id,
        CustomerId $customerId,
        TimeSlot $slot,
        PartySize $size,
        DateTimeImmutable $now,
    ): self;
    // ↑ Starts in Pending status. "request" reads better than "create".

    // ──────────────────────────────────────────────────────────────
    // State transitions — each enforces the state machine
    // ──────────────────────────────────────────────────────────────
    public function confirm(DateTimeImmutable $now): void;
    public function markSeated(DateTimeImmutable $now): void;
    public function complete(DateTimeImmutable $now): void;
    public function markNoShow(DateTimeImmutable $now): void;
    public function cancel(CancellationReason $reason, DateTimeImmutable $now): void;
    public function reschedule(TimeSlot $newSlot, DateTimeImmutable $now): void;

    // ──────────────────────────────────────────────────────────────
    // Queries — read-only
    // ──────────────────────────────────────────────────────────────
    public function id(): ReservationId;
    public function customerId(): CustomerId;
    public function timeSlot(): TimeSlot;
    public function partySize(): PartySize;
    public function status(): ReservationStatus;
    public function isCancellable(): bool;
    public function isActive(): bool; // not in a terminal state
}
