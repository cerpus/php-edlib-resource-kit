<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Exception\MappingException;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\Image;

interface ImageMapperInterface
{
    /**
     * @param array<mixed> $data
     * @throws MappingException
     */
    public function map(array $data): Image|null;
}
