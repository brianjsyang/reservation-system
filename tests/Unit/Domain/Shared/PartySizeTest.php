<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Reservations\Domain\Shared\PartySize;

final class PartySizeTest extends TestCase
{

    public function test_it_creates_a_valid_party_size(): void
    {
        $size = new PartySize(4);
        $this->assertSame(4, $size->size());
    }

    // Data provider feeds multiple cases into one test method
    public static function invalidSizes(): array
    {
        return [
            'zero'      => [0],
            'negative'  => [-1],
            'min'       => [PartySize::MIN],
            'too small' => [PartySize::MIN - 1],
            'max'       => [PartySize::MAX],
            'too large' => [PartySize::MAX + 1],
        ];
    }

    // Use the data provider!
    #[DataProvider('invalidSizes')]
    public function test_it_rejects_invalid_sizes(int $size): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PartySize($size);
    }

    public function test_it_considers_equal_sizes_as_equal(): void
    {
        $res = (new PartySize(10))->equals(new PartySize(10));
        $this->assertTrue($res);
    }

    public function test_it_considers_different_sizes_as_not_equal(): void
    {
        $res = (new PartySize(10))->equals(new PartySize(11));
        $this->assertFalse($res);
    }

    public function test_party_size_exceeds_other(): void
    {
        $res = (new PartySize(10))->exceeds(new PartySize(9));
        $this->assertTrue($res);
    }
}
