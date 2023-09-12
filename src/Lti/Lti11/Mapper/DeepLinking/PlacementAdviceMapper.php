<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItemPlacement;

final readonly class PlacementAdviceMapper implements PlacementAdviceMapperInterface
{
    public function map(array $data): ContentItemPlacement|null
    {
        return new ContentItemPlacement(
            Prop::getDisplayWidth($data),
            Prop::getDisplayHeight($data),
            Prop::getPresentationDocumentTarget($data),
            Prop::getWindowTarget($data),
        );
    }
}
