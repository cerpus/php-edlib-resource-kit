<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\Image;

final readonly class ImageSerializer implements ImageSerializerInterface
{
    public function serialize(Image $image): array
    {
        $serialized = [
            '@id' => $image->getUri(),
        ];

        if ($image->getWidth() !== null) {
            $serialized[Prop::WIDTH] = $image->getWidth();
        }

        if ($image->getHeight() !== null) {
            $serialized[Prop::HEIGHT] = $image->getHeight();
        }

        return $serialized;
    }
}
