<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Event\Data;

use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetStatus;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedStatus;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function testConstructorThrowsOnUnsupportedStatus(): void
    {
        $this->expectException(UnsupportedStatus::class);
        $this->expectExceptionMessage('Unsupported status "Foo Status"');

        new Status('Foo Status');
    }

    public function testFromBugsNetHandlesOpenStatus(): void
    {
        $this->assertSame('Open', Status::fromBugsNet('Open')->toString());
    }

    public function testFromBugsNetHandlesReOpenedStatus(): void
    {
        $this->assertSame('Re-Opened', Status::fromBugsNet('Re-Opened')->toString());
    }

    public function testFromBugsNetHandlesAnalyzedStatus(): void
    {
        $this->assertSame('Analyzed', Status::fromBugsNet('Analyzed')->toString());
    }

    public function testFromBugsNetHandlesVerifiedStatus(): void
    {
        $this->assertSame('Verified', Status::fromBugsNet('Verified')->toString());
    }

    public function testFromBugsNetHandlesAssignedStatus(): void
    {
        $this->assertSame('Assigned', Status::fromBugsNet('Assigned')->toString());
    }

    public function testFromBugsNetHandlesClosedStatus(): void
    {
        $this->assertSame('Closed', Status::fromBugsNet('Closed')->toString());
    }

    public function testFromBugsNetHandlesFeedbackStatus(): void
    {
        $this->assertSame('Waiting on Feedback', Status::fromBugsNet('Feedback')->toString());
    }

    public function testFromBugsNetHandlesFeedbackStatusWithDays(): void
    {
        $this->assertSame('Waiting on Feedback', Status::fromBugsNet('Feedback 2 days')->toString());
    }

    public function testFromBugsNetHandlesNoFeedbackStatus(): void
    {
        $this->assertSame('No Feedback', Status::fromBugsNet('No Feedback')->toString());
    }

    public function testFromBugsNetHandlesSuspendedStatus(): void
    {
        $this->assertSame('Suspended', Status::fromBugsNet('Suspended')->toString());
    }

    public function testFromBugsNetHandlesNotABugStatus(): void
    {
        $this->assertSame('Not a Bug', Status::fromBugsNet('Not a bug')->toString());
    }

    public function testFromBugsNetHandlesDuplicateStatus(): void
    {
        $this->assertSame('Duplicate', Status::fromBugsNet('Duplicate')->toString());
    }

    public function testFromBugsNetHandlesWontFixStatus(): void
    {
        $this->assertSame('Won\'t fix', Status::fromBugsNet('Wont fix')->toString());
    }

    public function testFromBugsNetThrowsOnUnsupportedStatus(): void
    {
        $this->expectException(UnsupportedBugsNetStatus::class);
        $this->expectExceptionMessage('Unsupported status "Unsupported Status" from bugs.php');

        Status::fromBugsNet('Unsupported Status');
    }
}
