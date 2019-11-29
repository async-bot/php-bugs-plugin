<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Event\Listener;

use Amp\Promise;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;

interface NewBugs
{
    /**
     * @return Promise<null>
     */
    public function __invoke(Bugs $bugs): Promise;
}
