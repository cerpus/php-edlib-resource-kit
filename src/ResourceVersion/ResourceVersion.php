<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\ResourceVersion;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class ResourceVersion
{
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    /**
     * @throws Exception
     */
    public function __construct(
        private string $versionId,
        private string $resourceId,
        private string $externalSystemName,
        private string $externalSystemId,
        private string $title,
        private string|null $description,
        private bool $published,
        private bool $listed,
        private string|null $language,
        private string|null $contentType,
        private string|null $license,
        private string $ownerId,
        DateTimeInterface $createdAt,
        DateTimeInterface $updatedAt,
        private int|float|null $maxScore,
        private bool $isDraft,
        private array $extra = [],
    ) {
        $this->createdAt = DateTimeImmutable::createFromInterface($createdAt);
        $this->updatedAt = DateTimeImmutable::createFromInterface($updatedAt);
    }

    public function getVersionId(): string
    {
        return $this->versionId;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function getExternalSystemName(): string
    {
        return $this->externalSystemName;
    }

    public function getExternalSystemId(): string
    {
        return $this->externalSystemId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function isListed(): bool
    {
        return $this->listed;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getMaxScore(): float|int|null
    {
        return $this->maxScore;
    }

    public function isDraft(): bool
    {
        return $this->isDraft;
    }

    public function getExtra(): array
    {
        return $this->extra;
    }
}
