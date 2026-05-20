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
use Reservations\Domain\Reservation\Exception\InvalidReservationStateException;

final class Reservation
{

    // ──────────────────────────────────────────────────────────────
    // Construction — only via named factory, never `new` from outside
    // ──────────────────────────────────────────────────────────────
    private function __construct(
        private readonly ReservationId $id,
        private readonly CustomerId $customerId,
        private TimeSlot $slot,
        private readonly PartySize $partySize,
        private ReservationStatus $status,

        // timestamp fields
        private readonly DateTimeImmutable $createdAt,  // createdAt is set once and never changed
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $confirmedAt = null,
        private ?DateTimeImmutable $seatedAt = null,
        private ?DateTimeImmutable $completedAt = null,
        private ?DateTimeImmutable $cancelledAt = null,
        private ?DateTimeImmutable $noShowAt = null,
    ) {}

    public static function request(
        ReservationId $id,
        CustomerId $customerId,
        TimeSlot $slot,
        PartySize $partySize,
        DateTimeImmutable $now,
    ): self {
        return new self(
            id: $id,
            customerId: $customerId,
            slot: $slot,
            partySize: $partySize,
            status: ReservationStatus::Pending,
            createdAt: $now,
            updatedAt: $now,
        );
    }
    // ↑ Starts in Pending status. "request" reads better than "create".

    // ──────────────────────────────────────────────────────────────
    //
    // State transitions — each enforces the state machine
    //
    // Logic that decides whether a reservation status can be updated is done ELSEWHERE!
    //      * Reservation class can simply UPDATE the status, not worry about the logic.
    // ──────────────────────────────────────────────────────────────
    public function confirm(DateTimeImmutable $now): void
    {
        if ($this->status !== ReservationStatus::Pending) {
            throw InvalidReservationStateException::cannotConfirmFrom($this->status);
        }
        $this->status = ReservationStatus::Confirmed;
        $this->confirmedAt = $now;   // ← new fact
        $this->updatedAt   = $now;   // ← "something changed"
        // $this->createdAt is NEVER touched here
    }
    // public function markSeated(DateTimeImmutable $now): void;
    // public function complete(DateTimeImmutable $now): void;
    // public function markNoShow(DateTimeImmutable $now): void;
    // public function cancel(CancellationReason $reason, DateTimeImmutable $now): void;
    // public function reschedule(TimeSlot $newSlot, DateTimeImmutable $now): void;

    // ──────────────────────────────────────────────────────────────
    // Queries — read-only
    // ──────────────────────────────────────────────────────────────
    public function id(): ReservationId
    {
        return $this->id;
    }

    public function customerId(): CustomerId
    {
        return $this->customerId;
    }

    public function slot(): TimeSlot
    {
        return $this->slot;
    }

    public function partySize(): PartySize
    {
        return $this->partySize;
    }

    public function status(): ReservationStatus
    {
        return $this->status;
    }

    // public function isCancellable(): bool;
    // public function isActive(): bool; // not in a terminal state
}
