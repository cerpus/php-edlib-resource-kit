<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;

final readonly class LtiLinkItemSerializer implements LtiLinkItemSerializerInterface
{
    public function __construct(
        private ContentItemSerializerInterface $serializer = new ContentItemSerializer(),
        private LineItemSerializerInterface $lineItemSerializer = new LineItemSerializer(),
    ) {
    }

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

        if (!empty($item->getCustom())) {
            $serialized[Prop::CUSTOM] = $item->getCustom();
        }

        return $serialized;
    }
}
