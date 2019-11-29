<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Exception;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetStatus;
use PHPUnit\Framework\TestCase;

final class UnsupportedBugsNetStatusTest extends TestCase
{
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $this->expectException(UnsupportedBugsNetStatus::class);
        $this->expectExceptionMessage('Unsupported status "STATUS" from bugs.php');

        throw new UnsupportedBugsNetStatus('STATUS');
    }
}
