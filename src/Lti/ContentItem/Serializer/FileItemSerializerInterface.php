<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;

interface FileItemSerializerInterface
{
    /**
     * @return array<mixed>
     */
    public function serialize(FileItem $item): array;
}
