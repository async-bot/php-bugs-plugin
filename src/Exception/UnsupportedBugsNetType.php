<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Exception;

final class UnsupportedBugsNetType extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf('Unsupported type "%s" from bugs.php', $type));
    }
}
