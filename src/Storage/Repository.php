<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Storage;

use Amp\Promise;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;

interface Repository
{
    /**
     * @return Promise<int|null>
     */
    public function getLastId(): Promise;

    /**
     * @return Promise<null>
     */
    public function setLastId(Bug $bug): Promise;
}
