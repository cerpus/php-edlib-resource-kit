<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;

interface ContentItemsSerializerInterface
{
    /**
     * @return array<mixed>
     */
    public function serialize(ContentItems $items): array;
}
