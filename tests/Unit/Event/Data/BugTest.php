<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Event\Data;

use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use PHPUnit\Framework\TestCase;

final class BugTest extends TestCase
{
    private Bug $bug;

    private Bug $bugWithNulls;

    public function setUp(): void
    {
        $this->bug = new Bug(
            12345,
            'https://example.com',
            new \DateTimeImmutable('2019-01-02 12:13:00'),
            new \DateTimeImmutable('2019-01-03 12:13:00'),
            'The package',
            new Type(Type::BUG),
            new Status(Status::OPEN),
            '7.4.0beta2',
            'Windows',
            'opcache_compile_file(__FILE__); segfaults',
        );

        $this->bugWithNulls = new Bug(
            12345,
            'https://example.com',
            new \DateTimeImmutable('2019-01-02 12:13:00'),
            null,
            'The package',
            new Type(Type::BUG),
            new Status(Status::OPEN),
            '7.4.0beta2',
            null,
            'opcache_compile_file(__FILE__); segfaults',
        );
    }

    public function testGetId(): void
    {
        $this->assertSame(12345, $this->bug->getId());
    }

    public function testGetUrl(): void
    {
        $this->assertSame('https://example.com', $this->bug->getUrl());
    }

    public function testGetTimestamp(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->bug->getTimestamp());
    }

    public function testGetLastModified(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->bug->getLastModified());
    }

    public function testGetLastModifiedWhenNotModified(): void
    {
        $this->assertNull($this->bugWithNulls->getLastModified());
    }

    public function testGetPackage(): void
    {
        $this->assertSame('The package', $this->bug->getPackage());
    }

    public function testGetType(): void
    {
        $this->assertInstanceOf(Type::class, $this->bug->getType());
    }

    public function testGetStatus(): void
    {
        $this->assertInstanceOf(Status::class, $this->bug->getStatus());
    }

    public function testGetPhpVersion(): void
    {
        $this->assertSame('7.4.0beta2', $this->bug->getPhpVersion());
    }

    public function testGetOs(): void
    {
        $this->assertSame('Windows', $this->bug->getOs());
    }

    public function testGetOsWhenNotProvided(): void
    {
        $this->assertNull($this->bugWithNulls->getOs());
    }

    public function testGetSummary(): void
    {
        $this->assertSame('opcache_compile_file(__FILE__); segfaults', $this->bug->getSummary());
    }
}
