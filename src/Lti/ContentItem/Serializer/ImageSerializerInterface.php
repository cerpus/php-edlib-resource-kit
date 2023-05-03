<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;

interface ImageSerializerInterface
{
    /**
     * @return array<mixed>
     */
    public function serialize(Image $image): array;
}
