<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\ResourceVersion;

use Cerpus\EdlibResourceKit\Exception\ResourceNotFoundException;

interface ResourceVersionManagerInterface
{
    /**
     * @throws ResourceNotFoundException
     */
    public function getCurrentVersion(string $resourceId): ResourceVersion;

    /**
     * @throws ResourceNotFoundException
     */
    public function getVersion(
        string $resourceId,
        string $versionId,
    ): ResourceVersion;
}
