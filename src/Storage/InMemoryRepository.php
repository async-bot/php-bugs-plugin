<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Storage;

use Amp\Promise;
use Amp\Success;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;

final class InMemoryRepository implements Repository
{
    private ?int $lastId = null;

    /**
     * @return Promise<int|null>
     */
    public function getLastId(): Promise
    {
        return new Success($this->lastId);
    }

    /**
     * @return Promise<null>
     */
    public function setLastId(Bug $bug): Promise
    {
        $this->lastId = $bug->getId();

        return new Success();
    }
}
