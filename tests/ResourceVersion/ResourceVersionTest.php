<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\ResourceVersion;

use DateTimeImmutable;
use DateTimeInterface;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersion;
use PHPUnit\Framework\TestCase;

final class ResourceVersionTest extends TestCase
{
    private ResourceVersion $version;

    protected function setUp(): void
    {
        $this->version = new ResourceVersion(
            '123',
            '456',
            'test',
            '789',
            'The title',
            'The description',
            true,
            false,
            'eng',
            'h5p.draganddrop',
            'cc0',
            '012',
            new DateTimeImmutable('2022-01-01T00:00:00Z'),
            new DateTimeImmutable('2022-02-01T00:00:00Z'),
            1,
            true,
            [
                'authorOverwrite' => 'John',
            ],
        );
    }

    public function testGetVersionId(): void
    {
        $this->assertSame('123', $this->version->getVersionId());
    }

    public function testGetResourceId(): void
    {
        $this->assertSame('456', $this->version->getResourceId());
    }

    public function testGetTitle(): void
    {
        $this->assertSame('The title', $this->version->getTitle());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('The description', $this->version->getDescription());
    }

    public function testIsPublished(): void
    {
        $this->assertTrue($this->version->isPublished());
    }

    public function testIsListed(): void
    {
        $this->assertFalse($this->version->isListed());
    }

    public function testGetLanguage(): void
    {
        $this->assertSame('eng', $this->version->getLanguage());
    }

    public function testGetContentType(): void
    {
        $this->assertSame('h5p.draganddrop', $this->version->getContentType());
    }

    public function testGetLicense(): void
    {
        $this->assertSame('cc0', $this->version->getLicense());
    }

    public function testGetOwnerId(): void
    {
        $this->assertSame('012', $this->version->getOwnerId());
    }

    public function testGetCreatedAt(): void
    {
        $this->assertSame(
            '2022-01-01T00:00:00+00:00',
            $this->version->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function testGetUpdatedAt(): void
    {
        $this->assertSame(
            '2022-01-01T00:00:00+00:00',
            $this->version->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function testGetExtra(): void
    {
        $this->assertSame([
            'authorOverwrite' => 'John',
        ], $this->version->getExtra());
    }
}
