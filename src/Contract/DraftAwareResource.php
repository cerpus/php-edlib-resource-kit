<?php

namespace Cerpus\EdlibResourceKit\Contract;

interface DraftAwareResource extends EdlibResource
{
    /**
     * Note about isPublished and isDraft:
     * A resource can have multiple versions. The value of isPublished of last version without isDraft=true
     * determines if a resource is published. If a resource is not published, it will not be accessible through
     * LTI, but it will show up in my content.
     */

    public function isDraft(): bool;
}
