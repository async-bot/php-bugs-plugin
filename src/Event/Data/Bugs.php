<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Event\Data;

final class Bugs implements \Iterator, \Countable
{
    /** @var array<Bug> */
    private array $bugs;

    public function __construct(Bug ...$bugs)
    {
        $this->bugs = $bugs;
    }

    public function current(): Bug
    {
        return current($this->bugs);
    }

    public function next(): void
    {
        next($this->bugs);
    }

    public function key(): ?int
    {
        return key($this->bugs);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function rewind(): void
    {
        reset($this->bugs);
    }

    public function count(): int
    {
        return count($this->bugs);
    }

    public function reduceToUnseen(?int $lastSeenId): self
    {
        if ($lastSeenId === null) {
            return clone $this;
        }

        return new self(...array_filter($this->bugs, fn (Bug $bug) => $bug->getId() > $lastSeenId));
    }

    public function getLast(): ?Bug
    {
        if (!$this->bugs) {
            return null;
        }

        end($this->bugs);

        return current($this->bugs);
    }
}
