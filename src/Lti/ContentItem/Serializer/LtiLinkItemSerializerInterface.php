<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;

interface LtiLinkItemSerializerInterface
{
    /**
     * @return array<mixed>
     */
    public function serialize(LtiLinkItem $item): array;
}
