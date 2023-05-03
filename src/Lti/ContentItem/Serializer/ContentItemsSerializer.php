<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;

final readonly class ContentItemsSerializer implements ContentItemsSerializerInterface
{
    public function __construct(
        private ContentItemSerializerInterface $contentItemSerializer = new ContentItemSerializer(),
        private FileItemSerializerInterface $fileItemSerializer = new FileItemSerializer(),
        private LtiLinkItemSerializerInterface $ltiLinkItemSerializer = new LtiLinkItemSerializer(),
    ) {
    }

    public function serialize(ContentItems $items): array
    {
        $items = iterator_to_array($items);

        return [
            '@context' => ContentItems::CONTEXT,
            '@graph' => array_map(function (ContentItem $item) {
                if ($item instanceof FileItem) {
                    return $this->fileItemSerializer->serialize($item);
                }

                if ($item instanceof LtiLinkItem) {
                    return $this->ltiLinkItemSerializer->serialize($item);
                }

                return $this->contentItemSerializer->serialize($item);
            }, $items),
        ];
    }
}
