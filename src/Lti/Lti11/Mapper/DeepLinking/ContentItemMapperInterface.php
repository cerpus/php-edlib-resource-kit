<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Exception\MappingException;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItem;

interface ContentItemMapperInterface
{
    /**
     * @param array<mixed> $data
     * @throws MappingException
     */
    public function map(array $data): ContentItem;
}
