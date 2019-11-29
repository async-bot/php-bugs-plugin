<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Exception;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetType;
use PHPUnit\Framework\TestCase;

final class UnsupportedBugsNetTypeTest extends TestCase
{
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $this->expectException(UnsupportedBugsNetType::class);
        $this->expectExceptionMessage('Unsupported type "TYPE" from bugs.php');

        throw new UnsupportedBugsNetType('TYPE');
    }
}
