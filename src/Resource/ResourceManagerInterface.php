<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource;

use Cerpus\EdlibResourceKit\Contract\EdlibResource;

interface ResourceManagerInterface
{
    public function save(EdlibResource $resource): void;
}
