<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Contracts;

use DateTimeImmutable;
use Cerpus\EdlibResourceKit\Contract\EdlibResource;

final class EdlibResourceStub implements EdlibResource
{
    public function getExternalSystemName(): string
    {
        return 'test';
    }

    public function getExternalSystemId(): string
    {
        return '123';
    }

    public function getTitle(): string
    {
        return 'title';
    }

    public function getOwnerId(): string|null
    {
        return '321';
    }

    public function isPublished(): bool
    {
        return true;
    }

    public function isListed(): bool
    {
        return true;
    }

    public function getLanguage(): string|null
    {
        return 'eng';
    }

    public function getContentType(): string|null
    {
        return 'h5p.something';
    }

    public function getLicense(): string|null
    {
        return 'cc0';
    }

    public function getMaxScore(): int|float|null
    {
        return 1.0;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return new DateTimeImmutable('2022-01-01T00:00:00Z');
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return new DateTimeImmutable('2022-02-01T00:00:00Z');
    }

    public function getCollaborators(): array
    {
        return [
            '1234',
        ];
    }

    public function getEmailCollaborators(): array
    {
        return [
            'johnny.cash@example.com',
        ];
    }
}
