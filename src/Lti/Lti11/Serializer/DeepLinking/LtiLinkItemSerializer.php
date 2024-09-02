<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;

final readonly class LtiLinkItemSerializer implements LtiLinkItemSerializerInterface
{
    public function __construct(
        private ContentItemSerializer $serializer = new ContentItemSerializer(),
        private LineItemSerializerInterface $lineItemSerializer = new LineItemSerializer(),
    ) {
    }

    /**
     * @todo Handle the "custom" property
     */
    public function serialize(LtiLinkItem $item): array
    {
        $serialized = [
            ...$this->serializer->serialize($item),
            '@type' => 'LtiLinkItem',
        ];

        if ($item->getLineItem() !== null) {
            $serialized[Prop::LINE_ITEM] = $this
                ->lineItemSerializer
                ->serialize($item->getLineItem());
        }

        return $serialized;
    }
}
