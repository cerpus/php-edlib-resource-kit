<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;

interface ContentItemSerializerInterface
{
    /**
     * @return array<mixed>
     *     The expanded form JSON-LD representation of the LTI content item
     */
    public function serialize(ContentItem $item): array;
}
