<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;

final readonly class ContentItemPlacementSerializer implements ContentItemPlacementSerializerInterface
{
    public function serialize(ContentItemPlacement $placement): array
    {
        $serialized = [];

        if ($placement->getDisplayWidth() !== null) {
            $serialized[ContentItems::PROP_DISPLAY_WIDTH] = $placement->getDisplayWidth();
        }

        if ($placement->getDisplayHeight() !== null) {
            $serialized[ContentItems::PROP_DISPLAY_HEIGHT] = $placement->getDisplayHeight();
        }

        if ($placement->getPresentationDocumentTarget() !== null) {
            $serialized[ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET]
                = $placement->getPresentationDocumentTarget()->toShortName();
        }

        if ($placement->getWindowTarget() !== null) {
            $serialized[ContentItems::PROP_WINDOW_TARGET] = $placement->getWindowTarget();
        }

        return $serialized;
    }
}
