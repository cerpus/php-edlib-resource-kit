<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;

final readonly class FileItemSerializer implements FileItemSerializerInterface
{
    public function __construct(
        private ContentItemSerializerInterface $serializer = new ContentItemSerializer(),
    ) {
    }

    public function serialize(FileItem $item): array
    {
        $serialized = [
            ...$this->serializer->serialize($item),
            '@type' => 'FileItem',
        ];

        if ($item->getCopyAdvice() !== null) {
            $serialized[ContentItems::PROP_COPY_ADVICE] = $item->getCopyAdvice();
        }

        if ($item->getExpiresAt() !== null) {
            $serialized[ContentItems::PROP_EXPIRES_AT] = $item
                ->getExpiresAt()
                ->format('c'); // TODO: is this correct?
        }

        return $serialized;
    }
}
