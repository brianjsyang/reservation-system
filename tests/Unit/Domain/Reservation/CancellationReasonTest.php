<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reservation;

use PHPUnit\Framework\TestCase;
use Reservations\Domain\Reservation\CancellationCategory;
use Reservations\Domain\Reservation\CancellationReason;

final class CancellationReasonTest extends TestCase
{
    // --- Cancellation Reasons -----------------------------------------

    public function test_customer_requested_creates_correct_category(): void
    {
        $reason = CancellationReason::customerRequested('kid sick');
        $this->assertSame(CancellationCategory::CustomerRequested, $reason->category);
        $this->assertSame('kid sick', $reason->note);
    }

    public function test_note_is_optional(): void
    {
        $reason = CancellationReason::customerRequested();
        $this->assertNull($reason->note);
    }

    // TODO: test_cancel_records_reason_and_time
}
