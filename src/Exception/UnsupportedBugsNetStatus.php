<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Exception;

final class UnsupportedBugsNetStatus extends Exception
{
    public function __construct(string $status)
    {
        parent::__construct(sprintf('Unsupported status "%s" from bugs.php', $status));
    }
}
