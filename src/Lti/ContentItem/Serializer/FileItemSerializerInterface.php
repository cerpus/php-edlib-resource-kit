<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;

interface FileItemSerializerInterface
{
    /**
     * @return array<mixed>
     *     The expanded form JSON-LD representation of an LTI file item
     */
    public function serialize(FileItem $item): array;
}
