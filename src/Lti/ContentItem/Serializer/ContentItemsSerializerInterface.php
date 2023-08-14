<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;

interface ContentItemsSerializerInterface
{
    /**
     * @return array<mixed>
     *     The compact form JSON-LD representation of the LTI content items
     */
    public function serialize(ContentItems $items): array;
}
