<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;

final readonly class ImageSerializer implements ImageSerializerInterface
{
    public function serialize(Image $image): array
    {
        $serialized = [
            '@id' => $image->getUri(),
        ];

        if ($image->getWidth() !== null) {
            $serialized[ContentItems::PROP_WIDTH] = $image->getWidth();
        }

        if ($image->getHeight() !== null) {
            $serialized[ContentItems::PROP_HEIGHT] = $image->getHeight();
        }

        return $serialized;
    }
}
