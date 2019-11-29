<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Exception;

final class UnsupportedStatus extends Exception
{
    public function __construct(string $status)
    {
        parent::__construct(sprintf('Unsupported status "%s"', $status));
    }
}
