<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;

interface ContentItemPlacementSerializerInterface
{
    /**
     * @return array<mixed>
     *     The expanded form JSON-LD representation of the content item
     *     placement
     */
    public function serialize(ContentItemPlacement $placement): array;
}
