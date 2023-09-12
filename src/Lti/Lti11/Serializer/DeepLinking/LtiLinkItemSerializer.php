<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;

final readonly class LtiLinkItemSerializer implements LtiLinkItemSerializerInterface
{
    public function __construct(
        private ContentItemSerializer $serializer = new ContentItemSerializer(),
    ) {
    }

    /**
     * @todo Handle the "custom" property
     */
    public function serialize(LtiLinkItem $item): array
    {
        return [
            ...$this->serializer->serialize($item),
            '@type' => 'LtiLinkItem',
        ];
    }
}
