<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Event\Data;

use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetType;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedType;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testConstructorThrowsOnUnsupportedType(): void
    {
        $this->expectException(UnsupportedType::class);
        $this->expectExceptionMessage('Unsupported type "Foo Type"');

        new Type('Foo Type');
    }

    public function testFromBugsNetHandlesDocType(): void
    {
        $this->assertSame('Documentation Problem', Type::fromBugsNet('Doc')->toString());
    }

    public function testFromBugsNetHandlesBugType(): void
    {
        $this->assertSame('Bug', Type::fromBugsNet('Bug')->toString());
    }

    public function testFromBugsNetHandlesReqType(): void
    {
        $this->assertSame('Feature Request', Type::fromBugsNet('Req')->toString());
    }

    public function testFromBugsNetHandlesSecBugType(): void
    {
        $this->assertSame('Security', Type::fromBugsNet('Sec Bug')->toString());
    }

    public function testFromBugsNetThrowsOnUnsupportedType(): void
    {
        $this->expectException(UnsupportedBugsNetType::class);
        $this->expectExceptionMessage('Unsupported type "Unsupported Type" from bugs.php');

        Type::fromBugsNet('Unsupported Type');
    }
}
