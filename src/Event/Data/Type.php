<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Event\Data;

use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedBugsNetType;
use AsyncBot\Plugin\PhpBugs\Exception\UnsupportedType;

final class Type
{
    public const BUG                   = 'Bug';
    public const FEATURE_REQUEST       = 'Feature Request';
    public const DOCUMENTATION_PROBLEM = 'Documentation Problem';
    public const SECURITY              = 'Security';

    private string $type;

    public function __construct(string $type)
    {
        if (!in_array($type, (new \ReflectionClass($this))->getConstants())) {
            throw new UnsupportedType($type);
        }

        $this->type = $type;
    }

    public static function fromBugsNet(string $type): self
    {
        switch ($type) {
            case 'Doc':
                return new self(self::DOCUMENTATION_PROBLEM);

            case 'Bug':
                return new self(self::BUG);

            case 'Req':
                return new self(self::FEATURE_REQUEST);

            case 'Sec Bug':
                return new self(self::SECURITY);
        }

        throw new UnsupportedBugsNetType($type);
    }
    
    public function equals(Type $type): bool
    {
        return $type->toString() === $this->type;
    }

    public function toString(): string
    {
        return $this->type;
    }
}
