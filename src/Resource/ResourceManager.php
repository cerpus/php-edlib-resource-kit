<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource;

use Cerpus\EdlibResourceKit\Exception\RuntimeException;
use Cerpus\PubSub\PubSub;
use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use JsonException;
use function json_encode;

final class ResourceManager implements ResourceManagerInterface
{
    public function __construct(
        private PubSub $pubSub,
        private ResourceSerializer $resourceSerializer,
    ) {
    }

    public function save(EdlibResource $resource): void
    {
        try {
            $data = json_encode(
                $this->resourceSerializer->serialize($resource),
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new RuntimeException('Could not json_encode resource', 0, $e);
        }

        $this->pubSub->publish('edlibResourceUpdate', $data);
    }
}
