<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Exception\MappingException;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItem;

interface ContentItemsMapperInterface
{
    /**
     * @param array<mixed> $data
     * @return array<ContentItem>
     * @throws MappingException
     */
    public function map(array $data): array;
}
