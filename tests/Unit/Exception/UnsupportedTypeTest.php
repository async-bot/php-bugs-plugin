<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Exception;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedType;
use PHPUnit\Framework\TestCase;

final class UnsupportedTypeTest extends TestCase
{
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $this->expectException(UnsupportedType::class);
        $this->expectExceptionMessage('Unsupported type "TYPE"');

        throw new UnsupportedType('TYPE');
    }
}
