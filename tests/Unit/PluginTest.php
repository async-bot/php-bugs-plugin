<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit;

use Amp\Loop;
use Amp\Success;
use AsyncBot\Core\Logger\Logger;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use AsyncBot\Plugin\PhpBugs\Event\Listener\NewBugs;
use AsyncBot\Plugin\PhpBugs\Exception\UnexpectedHtmlFormat;
use AsyncBot\Plugin\PhpBugs\Plugin;
use AsyncBot\Plugin\PhpBugs\Retriever\Retriever;
use AsyncBot\Plugin\PhpBugs\Storage\Repository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class PluginTest extends TestCase
{
    private MockObject $psrLogger;

    private MockObject $retriever;

    private MockObject $repository;

    private Plugin $plugin;

    public function setUp(): void
    {
        $this->psrLogger  = $this->createMock(LoggerInterface::class);
        $this->retriever  = $this->createMock(Retriever::class);
        $this->repository = $this->createMock(Repository::class);

        $this->plugin = new Plugin(new Logger($this->psrLogger), $this->retriever, $this->repository);
    }

    public function testRunLogsErrorWhenExceptionIsThrown(): void
    {
        $exception = new UnexpectedHtmlFormat('id');

        $this->retriever
            ->expects($this->once())
            ->method('retrieve')
            ->willThrowException($exception)
        ;

        $this->psrLogger
            ->expects($this->once())
            ->method('error')
            ->with('Exception', ['exception' => $exception])
        ;

        Loop::run(function () {
            Loop::defer(fn() => Loop::stop());

            yield $this->plugin->run();
        });
    }

    public function testRunReschedulesAfterException(): void
    {
        $this->retriever
            ->expects($this->exactly(2))
            ->method('retrieve')
            ->willThrowException(new UnexpectedHtmlFormat('id'))
        ;

        $this->psrLogger
            ->expects($this->exactly(2))
            ->method('error')
        ;

        Loop::run(function () {
            Loop::delay(1500, fn() => Loop::stop());

            $plugin = new Plugin(new Logger($this->psrLogger), $this->retriever, $this->repository, new \DateInterval('PT1S'));

            yield $plugin->run();
        });
    }

    public function testOnStatusChangeLogsNewListenerRegistration(): void
    {
        $this->psrLogger
            ->expects($this->once())
            ->method('info')
            ->with('New listener registered')
        ;

        $this->plugin->onNewBugs($this->createMock(NewBugs::class));
    }

    public function testRunDoesNotExecuteListenerWhenThereAreNoPreviousBugs(): void
    {
        $this->retriever
            ->expects($this->once())
            ->method('retrieve')
            ->willReturn(new Success(new Bugs(new Bug(
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
            ))))
        ;

        $this->repository
            ->expects($this->once())
            ->method('getLastId')
            ->willReturn(new Success(null))
        ;

        $this->repository
            ->expects($this->once())
            ->method('setLastId')
            ->willReturn(new Success())
        ;

        $listener = $this->createMock(NewBugs::class);

        $listener
            ->expects($this->never())
            ->method('__invoke')
        ;

        $this->plugin->onNewBugs($listener);

        Loop::run(function () {
            Loop::defer(fn() => Loop::stop());

            yield $this->plugin->run();
        });
    }

    public function testRunDoesNotExecuteListenerWhenThereAreNoNewBugs(): void
    {
        $this->retriever
            ->expects($this->once())
            ->method('retrieve')
            ->willReturn(new Success(new Bugs(new Bug(
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
            ))))
        ;

        $this->repository
            ->expects($this->once())
            ->method('getLastId')
            ->willReturn(new Success(13))
        ;

        $this->repository
            ->expects($this->never())
            ->method('setLastId')
        ;

        $listener = $this->createMock(NewBugs::class);

        $listener
            ->expects($this->never())
            ->method('__invoke')
        ;

        $this->plugin->onNewBugs($listener);

        Loop::run(function () {
            Loop::defer(fn() => Loop::stop());

            yield $this->plugin->run();
        });
    }

    public function testRunExecutesListener(): void
    {
        $this->retriever
            ->expects($this->once())
            ->method('retrieve')
            ->willReturn(new Success(new Bugs(new Bug(
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
            ))))
        ;

        $this->repository
            ->expects($this->once())
            ->method('getLastId')
            ->willReturn(new Success(12))
        ;

        $this->repository
            ->expects($this->once())
            ->method('setLastId')
            ->willReturn(new Success())
        ;

        $listener = $this->createMock(NewBugs::class);

        $listener
            ->expects($this->once())
            ->method('__invoke')
        ;

        $this->plugin->onNewBugs($listener);

        Loop::run(function () {
            Loop::defer(fn() => Loop::stop());

            yield $this->plugin->run();
        });
    }
}
