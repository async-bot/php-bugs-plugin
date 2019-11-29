<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Retriever;

use Amp\Promise;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;

interface Retriever
{
    /**
     * @return Promise<Bugs>
     */
    public function retrieve(): Promise;
}
