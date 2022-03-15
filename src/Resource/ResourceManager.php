<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource;

use Cerpus\EdlibResourceKit\Util\Json;
use Cerpus\PubSub\PubSub;
use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;

final class ResourceManager implements ResourceManagerInterface
{
    public function __construct(
        private PubSub $pubSub,
        private ResourceSerializer $resourceSerializer,
    ) {
    }

    public function save(EdlibResource $resource): void
    {
        $data = Json::encode($this->resourceSerializer->serialize($resource));

        $this->pubSub->publish('edlibResourceUpdate', $data);
    }
}
