<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit;

use Cerpus\EdlibResourceKit\Resource\ResourceManagerInterface;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManagerInterface;

interface ResourceKitInterface
{
    public function getResourceManager(): ResourceManagerInterface;

    public function getResourceVersionManager(): ResourceVersionManagerInterface;
}
