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
            $serialized[ContentItems::PROP_WIDTH] = [
                '@value' => $image->getWidth(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#integer',
            ];
        }

        if ($image->getHeight() !== null) {
            $serialized[ContentItems::PROP_HEIGHT] = [
                '@value' => $image->getHeight(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#integer',
            ];
        }

        return $serialized;
    }
}
