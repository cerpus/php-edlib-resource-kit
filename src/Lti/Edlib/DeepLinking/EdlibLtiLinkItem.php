<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;

use function array_values;

class EdlibLtiLinkItem extends LtiLinkItem
{
    private string|null $languageIso639_3 = null;

    private string|null $license = null;

    private bool|null $published = null;

    /**
     * @var list<string>
     */
    private array $tags = [];

    public function getLanguageIso639_3(): string|null
    {
        return $this->languageIso639_3;
    }

    public function withLanguageIso639_3(string|null $languageIso639_3): static
    {
        $self = clone $this;
        $self->languageIso639_3 = $languageIso639_3;

        return $self;
    }

    public function getLicense(): string|null
    {
        return $this->license;
    }

    public function withLicense(string|null $license): static
    {
        $self = clone $this;
        $self->license = $license;

        return $self;
    }

    public function isPublished(): bool|null
    {
        return $this->published;
    }

    public function withPublished(bool|null $published): static
    {
        $self = clone $this;
        $self->published = $published;

        return $self;
    }

    /**
     * @return list<string>
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function withTags(array $tags): static
    {
        $self = clone $this;
        $self->tags = array_values($tags);

        return $self;
    }
}
