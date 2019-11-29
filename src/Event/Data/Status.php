<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Event\Data;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetStatus;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedStatus;

final class Status
{
    public const CLOSED      = 'Closed';
    public const OPEN        = 'Open';
    public const REOPENED    = 'Re-Opened';
    public const CRITICAL    = 'Critical';
    public const VERIFIED    = 'Verified';
    public const ANALYZED    = 'Analyzed';
    public const ASSIGNED    = 'Assigned';
    public const FEEDBACK    = 'Waiting on Feedback';
    public const NO_FEEDBACK = 'No Feedback';
    public const SUSPENDED   = 'Suspended';
    public const NOT_A_BUG   = 'Not a Bug';
    public const DUPLICATE   = 'Duplicate';
    public const WONT_FIX    = 'Won\'t fix';

    private string $status;

    public function __construct(string $status)
    {
        if (!in_array($status, (new \ReflectionClass($this))->getConstants())) {
            throw new UnsupportedStatus($status);
        }

        $this->status = $status;
    }

    public static function fromBugsNet(string $status): self
    {
        if (preg_match('~^Feedback ?\d+~', $status)) {
            return new self(self::FEEDBACK);
        }

        switch ($status) {
            case 'Open':
                return new self(self::OPEN);

            case 'Re-Opened':
                return new self(self::REOPENED);

            case 'Analyzed':
                return new self(self::ANALYZED);

            case 'Verified':
                return new self(self::VERIFIED);

            case 'Assigned':
                return new self(self::ASSIGNED);

            case 'Closed':
                return new self(self::CLOSED);

            case 'Feedback':
                return new self(self::FEEDBACK);

            case 'No Feedback':
                return new self(self::NO_FEEDBACK);

            case 'Suspended':
                return new self(self::SUSPENDED);

            case 'Not a bug':
                return new self(self::NOT_A_BUG);

            case 'Duplicate':
                return new self(self::DUPLICATE);

            case 'Wont fix':
                return new self(self::WONT_FIX);
        }

        throw new UnsupportedBugsNetStatus($status);
    }

    public function toString(): string
    {
        return $this->status;
    }
}
