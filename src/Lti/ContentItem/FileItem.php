<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem;

use DateTimeImmutable;
use DateTimeInterface;

class FileItem extends ContentItem
{
    private readonly DateTimeImmutable $expiresAt;

    public function __construct(
        string $mediaType,
        private readonly bool|null $copyAdvice = null,
        DateTimeInterface|null $expiresAt = null,
        Image|null $icon = null,
        ContentItemPlacement|null $placementAdvice = null,
        string|null $text = null,
        Image|null $thumbnail = null,
        string|null $title = null,
        string|null $url = null,
    ) {
        $this->expiresAt = DateTimeImmutable::createFromInterface($expiresAt);

        parent::__construct(
            $mediaType,
            $icon,
            $placementAdvice,
            $text,
            $thumbnail,
            $title,
            $url,
        );
    }

    public function getCopyAdvice(): ?bool
    {
        return $this->copyAdvice;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }
}
