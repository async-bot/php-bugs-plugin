<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs;

use Amp\Loop;
use Amp\Promise;
use AsyncBot\Core\Logger\Logger;
use AsyncBot\Core\Plugin\Runnable;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Event\Listener\NewBugs as EventListener;
use AsyncBot\Plugin\PhpBugs\Retriever\Retriever;
use AsyncBot\Plugin\PhpBugs\Storage\Repository;
use function Amp\call;

final class Plugin implements Runnable
{
    private const DEFAULT_INTERVAL_IN_MS = 15_000;

    private Logger $logger;

    private Retriever $bugsRetriever;

    private Repository $repository;

    private int $intervalInMs;

    /** @var array<EventListener> */
    private array $listeners = [];

    public function __construct(
        Logger $logger,
        Retriever $bugsRetriever,
        Repository $repository,
        ?\DateInterval $interval = null
    ) {
        $this->logger        = $logger;
        $this->bugsRetriever = $bugsRetriever;
        $this->repository    = $repository;

        if ($interval === null) {
            $this->intervalInMs = self::DEFAULT_INTERVAL_IN_MS;

            return;
        }

        $currentTimestamp = new \DateTimeImmutable();
        $targetTimestamp  = (new \DateTimeImmutable())->add($interval);

        $this->intervalInMs  = ($targetTimestamp->getTimestamp() - $currentTimestamp->getTimestamp()) * 1000;
    }

    public function onNewBugs(EventListener $listener): void
    {
        $this->logger->registeredListener($this, __METHOD__);

        $this->listeners[] = $listener;
    }

    /**
     * @return Promise<null>
     */
    public function run(): Promise
    {
        return call(function() {
            try {
                yield $this->getNewBugs();
            } catch (\Throwable $e) {
                $this->logger->error('Exception', ['exception' => $e]);
            }

            Loop::delay($this->intervalInMs, function () {
                yield $this->run();
            });
        });
    }

    private function getNewBugs(): Promise
    {
        return call(function () {
            /** @var Bugs $bugs */
            $bugs = yield $this->bugsRetriever->retrieve();

            $lastId  = yield $this->repository->getLastId();
            $newBugs = $bugs->reduceToUnseen($lastId);

            if (!$newBugs->count()) {
                return;
            }

            yield $this->repository->setLastId($newBugs->getLast());

            if ($lastId === null) {
                return;
            }

            foreach ($this->listeners as $listener) {
                yield $listener($newBugs);
            }
        });
    }
}
