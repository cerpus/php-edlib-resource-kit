<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;

interface ContentItemSerializerInterface
{
    /**
     * @return array<mixed>
     */
    public function serialize(ContentItem $item): array;
}
