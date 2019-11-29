<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Storage;

use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use AsyncBot\Plugin\PhpBugs\Storage\InMemoryRepository;
use PHPUnit\Framework\TestCase;
use function Amp\Promise\wait;

final class InMemoryRepositoryTest extends TestCase
{
    public function testGetLastIdWhenNotSet(): void
    {
        $this->assertNull(wait((new InMemoryRepository())->getLastId()));
    }

    public function testGetLastId(): void
    {
        $repository = new InMemoryRepository();

        $repository->setLastId(new Bug(
            13,
            'https://example.com',
            new \DateTimeImmutable(),
            null,
            'package',
            new Type(Type::BUG),
            new Status(Status::FEEDBACK),
            '7.4',
            null,
            'summary',
        ));

        $this->assertSame(13, wait($repository->getLastId()));
    }
}
