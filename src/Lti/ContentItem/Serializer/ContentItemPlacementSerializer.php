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
            $serialized[ContentItems::PROP_DISPLAY_WIDTH] = [
                '@value' => $placement->getDisplayWidth(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#integer',
            ];
        }

        if ($placement->getDisplayHeight() !== null) {
            $serialized[ContentItems::PROP_DISPLAY_HEIGHT] = [
                '@value' => $placement->getDisplayHeight(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#integer',
            ];
        }

        if ($placement->getPresentationDocumentTarget() !== null) {
            $serialized[ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET] = [
                '@id' => $placement->getPresentationDocumentTarget()->toShortName(),
            ];
        }

        if ($placement->getWindowTarget() !== null) {
            $serialized[ContentItems::PROP_WINDOW_TARGET] = [
                '@value' => $placement->getWindowTarget(),
                '@type' => 'http://www.w3.org/2001/XMLSchema#normalizedString',
            ];
        }

        return $serialized;
    }
}
