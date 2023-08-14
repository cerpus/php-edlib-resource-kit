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
            '@type' => ContentItems::VOCAB . 'FileItem',
        ];

        if ($item->getCopyAdvice() !== null) {
            $serialized[ContentItems::PROP_COPY_ADVICE] = [
                '@value' => $item->getCopyAdvice(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#boolean',
            ];
        }

        if ($item->getExpiresAt() !== null) {
            $serialized[ContentItems::PROP_EXPIRES_AT] = [
                '@value' => $item->getExpiresAt()->format('c'), // TODO: is this correct?
                '@type' => 'http://www.w3.org/2001/XMLSchema#dateTime',
            ];
        }

        return $serialized;
    }
}
