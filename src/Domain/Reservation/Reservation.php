<?php

/**
 *
 * Aggregate root for everything reservation-related.
 * External code talks to Reservation; it never reaches around the back to mutate a TimeSlot or change a status directly.
 *
         ┌─────────┐
         │ Pending │
         └────┬────┘
              │ confirm()
              ▼
         ┌───────────┐
         │ Confirmed │────────────┐
         └────┬──────┘            │
              │ markSeated()      │ markNoShow()
              ▼                  ▼
         ┌────────┐         ┌─────────┐
         │ Seated │         │ NoShow  │  (terminal)
         └───┬────┘         └─────────┘
             │ complete()
             ▼
         ┌───────────┐
         │ Completed │  (terminal)
         └───────────┘

   Cancel transitions (from Pending or Confirmed):
         Pending/Confirmed ──cancel()──▶ Cancelled (terminal)

   Reschedule transitions (from Pending or Confirmed):
         Pending/Confirmed ──reschedule(newSlot)──▶ same status, new slot
 */

declare(strict_types=1);

namespace Reservations\Domain\Reservation;

use DateTimeImmutable;
use Reservations\Domain\Reservation\ReservationId;
use Reservations\Domain\Customer\CustomerId;
use Reservations\Domain\Shared\PartySize;
use Reservations\Domain\Shared\TimeSlot;
use Reservations\Domain\Reservation\ReservationStatus;
use Reservations\Domain\Reservation\CancellationReason;

final class Reservation
{
    private ReservationStatus $status = ReservationStatus::Pending;

    // ──────────────────────────────────────────────────────────────
    // Construction — only via named factory, never `new` from outside
    // ──────────────────────────────────────────────────────────────
    private function __construct(
        private readonly ReservationId $id,
        private readonly CustomerId $customerId,
        private readonly TimeSlot $slot,
        private readonly PartySize $size,
        private readonly DateTimeImmutable $now,
    ) {}

    public static function request(
        ReservationId $id,
        CustomerId $customerId,
        TimeSlot $slot,
        PartySize $size,
        DateTimeImmutable $now,
    ): self {
        return new self($id, $customerId, $slot, $size, $now);
    }
    // ↑ Starts in Pending status. "request" reads better than "create".

    // ──────────────────────────────────────────────────────────────
    // State transitions — each enforces the state machine
    // ──────────────────────────────────────────────────────────────
    // public function confirm(DateTimeImmutable $now): void;
    // public function markSeated(DateTimeImmutable $now): void;
    // public function complete(DateTimeImmutable $now): void;
    // public function markNoShow(DateTimeImmutable $now): void;
    // public function cancel(CancellationReason $reason, DateTimeImmutable $now): void;
    // public function reschedule(TimeSlot $newSlot, DateTimeImmutable $now): void;

    // ──────────────────────────────────────────────────────────────
    // Queries — read-only
    // ──────────────────────────────────────────────────────────────
    // public function id(): ReservationId;
    // public function customerId(): CustomerId;
    // public function timeSlot(): TimeSlot;
    // public function partySize(): PartySize;
    public function status(): ReservationStatus
    {
        return $this->status;
    }
    // public function isCancellable(): bool;
    // public function isActive(): bool; // not in a terminal state
}
