<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource;

use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\Resource\Exception\ResourceSaveFailedException;

interface ResourceManagerInterface
{
    /**
     * @throws ResourceSaveFailedException
     */
    public function save(EdlibResource $resource): void;
}
