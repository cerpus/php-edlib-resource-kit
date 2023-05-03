<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem;

/**
 * @see https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v1/contentitems%2Bjson/index.html#LtiLinkItem
 */
class LtiLinkItem extends ContentItem
{
    /**
     * @param array<mixed> $custom
     */
    public function __construct(
        string $mediaType = 'application/vnd.ims.lti.v1.ltilink',
        Image|null $icon = null,
        ContentItemPlacement|null $placementAdvice = null,
        Image|null $thumbnail = null,
        string|null $text = null,
        string|null $title = null,
        string|null $url = null,
        private readonly array $custom = [],
    ) {
        parent::__construct(
            $mediaType,
            $icon,
            $placementAdvice,
            $text,
            $thumbnail,
            $title,
            $url,
        );
    }

    public function getCustom(): array
    {
        return $this->custom;
    }
}
