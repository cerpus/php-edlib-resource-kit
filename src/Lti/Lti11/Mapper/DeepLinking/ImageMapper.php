<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\Image;

final readonly class ImageMapper implements ImageMapperInterface
{
    public function map(array $data): Image|null
    {
        if (!isset($data['@id'])) {
            return null;
        }

        return new Image(
            $data['@id'],
            Prop::getWidth($data),
            Prop::getHeight($data),
        );
    }
}
