<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Event\Data;

use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use PHPUnit\Framework\TestCase;

final class BugsTest extends TestCase
{
    private Bugs $bugs;

    public function setUp(): void
    {
        $this->bugs = new Bugs(
            new Bug(
                1,
                'https://example.com/1',
                new \DateTimeImmutable('2019-01-01 12:13:00'),
                null,
                'The package',
                new Type(Type::BUG),
                new Status(Status::OPEN),
                '7.4.0beta2',
                null,
                'opcache_compile_file(__FILE__); segfaults',
            ),
            new Bug(
                2,
                'https://example.com/2',
                new \DateTimeImmutable('2019-01-01 12:13:00'),
                null,
                'The package',
                new Type(Type::BUG),
                new Status(Status::OPEN),
                '7.4.0beta2',
                null,
                'opcache_compile_file(__FILE__); segfaults',
            ),
        );
    }

    public function testIterator(): void
    {
        $expectedIds = [1, 2];

        foreach ($this->bugs as $index => $bug) {
            $this->assertSame($expectedIds[$index], $bug->getId());
        }
    }

    public function testCount(): void
    {
        $this->assertCount(2, $this->bugs);
    }

    public function testReduceToSeen(): void
    {
        /** @var array<Bug> $bugs */
        $bugs = iterator_to_array($this->bugs->reduceToUnseen(1));

        $this->assertCount(1, $bugs);
        $this->assertSame(2, $bugs[0]->getId());
    }

    public function testReduceToSeenWhenLastSeenIdIsNull(): void
    {
        $this->assertCount(2, $this->bugs->reduceToUnseen(null));
    }

    public function testGetLast(): void
    {
        $this->assertSame(2, $this->bugs->getLast()->getId());
    }

    public function testGetLastReturnsNullOnEmptyCollection(): void
    {
        $this->assertNull((new Bugs())->getLast());
    }
}
