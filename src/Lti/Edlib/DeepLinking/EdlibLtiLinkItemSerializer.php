<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking\LtiLinkItemSerializer;
use Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking\LtiLinkItemSerializerInterface;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;

final readonly class EdlibLtiLinkItemSerializer implements LtiLinkItemSerializerInterface
{
    public function __construct(
        private LtiLinkItemSerializerInterface $serializer = new LtiLinkItemSerializer(),
    ) {
    }

    public function serialize(LtiLinkItem $item): array
    {
        $serialized = $this->serializer->serialize($item);

        if ($item instanceof EdlibLtiLinkItem) {
            if ($item->getLicense() !== null) {
                $serialized['license'] = $item->getLicense();
            }

            if ($item->getLanguageIso639_3() !== null) {
                $serialized['languageIso639_3'] = $item->getLanguageIso639_3();
            }

            if ($item->isPublished() !== null) {
                $serialized['published'] = $item->isPublished();
            }

            if (count($item->getTags()) > 1) {
                $serialized['tag'] = $item->getTags();
            } elseif (count($item->getTags()) === 1) {
                $serialized['tag'] = $item->getTags()[0];
            }
        }

        return $serialized;
    }
}
