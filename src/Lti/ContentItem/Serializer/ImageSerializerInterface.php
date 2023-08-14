<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;

interface ImageSerializerInterface
{
    /**
     * @return array<mixed>
     *     The expanded form JSON-LD representation of an image
     */
    public function serialize(Image $image): array;
}
