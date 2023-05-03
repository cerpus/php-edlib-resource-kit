<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\Exception\MissingMediaTypeException;
use stdClass;

interface ContentItemsMapperInterface
{
    /**
     * @throws MissingMediaTypeException
     */
    public function map(string|array|stdClass $dataOrJson): ContentItems;
}
