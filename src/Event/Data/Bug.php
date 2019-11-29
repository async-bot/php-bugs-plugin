<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Event\Data;

final class Bug
{
    private int $id;

    private string $url;

    private \DateTimeImmutable $timestamp;

    private ?\DateTimeImmutable $lastModified;

    private string $package;

    private Type $type;

    private Status $status;

    private string $phpVersion;

    private ?string $os;

    private string $summary;

    public function __construct(
        int $id,
        string $url,
        \DateTimeImmutable $timestamp,
        ?\DateTimeImmutable $lastModified,
        string $package,
        Type $type,
        Status $status,
        string $phpVersion,
        ?string $os,
        string $summary
    ) {
        $this->id           = $id;
        $this->url          = $url;
        $this->timestamp    = $timestamp;
        $this->lastModified = $lastModified;
        $this->package      = $package;
        $this->type         = $type;
        $this->status       = $status;
        $this->phpVersion   = $phpVersion;
        $this->os           = $os;
        $this->summary      = $summary;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }
}
