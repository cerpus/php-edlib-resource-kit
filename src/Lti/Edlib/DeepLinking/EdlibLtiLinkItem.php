<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;

class EdlibLtiLinkItem extends LtiLinkItem
{
    private string|null $languageIso639_3 = null;

    private string|null $license = null;

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
}
