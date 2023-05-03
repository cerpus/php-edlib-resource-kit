<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;

final readonly class ContentItemSerializer implements ContentItemSerializerInterface
{
    public function __construct(
        private ContentItemPlacementSerializerInterface $contentItemPlacementSerializer = new ContentItemPlacementSerializer(),
        private ImageSerializerInterface $imageSerializer = new ImageSerializer(),
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function serialize(ContentItem $item): array
    {
        $serialized = [
            '@type' => 'ContentItem',
            ContentItems::PROP_MEDIA_TYPE => $item->getMediaType(),
        ];

        if ($item->getPlacementAdvice() !== null) {
            $serialized[ContentItems::PROP_PLACEMENT_ADVICE] =
                $this
                    ->contentItemPlacementSerializer
                    ->serialize($item->getPlacementAdvice());
        }

        if ($item->getIcon() !== null) {
            $serialized[ContentItems::PROP_ICON] = $this
                ->imageSerializer
                ->serialize($item->getIcon());
        }

        if ($item->getText() !== null) {
            $serialized[ContentItems::PROP_TEXT] = $item->getText();
        }

        if ($item->getThumbnail() !== null) {
            $serialized[ContentItems::PROP_THUMBNAIL] = $this
                ->imageSerializer
                ->serialize($item->getThumbnail());
        }

        if ($item->getTitle() !== null) {
            $serialized[ContentItems::PROP_TITLE] = $item->getTitle();
        }

        if ($item->getUrl() !== null) {
            $serialized[ContentItems::PROP_URL] = $item->getUrl();
        }

        return $serialized;
    }
}
