<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;

interface LtiLinkItemSerializerInterface
{
    /**
     * @return array<mixed>
     *     The compact form JSON-LD representation of an LTI link item
     */
    public function serialize(LtiLinkItem $item): array;
}
