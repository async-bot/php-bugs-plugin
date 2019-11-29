<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Exception;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedStatus;
use PHPUnit\Framework\TestCase;

final class UnsupportedStatusTest extends TestCase
{
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $this->expectException(UnsupportedStatus::class);
        $this->expectExceptionMessage('Unsupported status "STATUS"');

        throw new UnsupportedStatus('STATUS');
    }
}
