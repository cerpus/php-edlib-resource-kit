<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;

interface LtiLinkItemSerializerInterface
{
    /**
     * @return array<mixed>
     *     The expanded form JSON-LD representation of an LTI link item
     */
    public function serialize(LtiLinkItem $item): array;
}
