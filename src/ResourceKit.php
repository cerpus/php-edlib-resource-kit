<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit;

use Cerpus\EdlibResourceKit\Resource\SynchronousResourceManager;
use Cerpus\PubSub\Connection\ConnectionFactory;
use Cerpus\PubSub\PubSub;
use Cerpus\EdlibResourceKit\Resource\ResourceManager;
use Cerpus\EdlibResourceKit\Resource\ResourceManagerInterface;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManager;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManagerInterface;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use InvalidArgumentException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ResourceKit implements ResourceKitInterface
{
    private PubSub|ConnectionFactory|null $pubSub;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private ResourceSerializer $resourceSerializer;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        PubSub|ConnectionFactory|null $pubSub = null,
        ClientInterface|null $httpClient = null,
        RequestFactoryInterface|null $requestFactory = null,
        ResourceSerializer $resourceSerializer = null,
        StreamFactoryInterface $streamFactory = null,
        private bool $synchronousResourceManager = false,
    ) {
        if (!$this->synchronousResourceManager && $pubSub === null) {
            throw new InvalidArgumentException(
                'Either $pubSub must be provided, or $synchronousResourceManager set to TRUE',
            );
        }

        $this->pubSub = $pubSub;
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->resourceSerializer = $resourceSerializer ?? new ResourceSerializer();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    public function getResourceManager(): ResourceManagerInterface
    {
        if ($this->synchronousResourceManager) {
            return new SynchronousResourceManager(
                $this->httpClient,
                $this->requestFactory,
                $this->resourceSerializer,
                $this->streamFactory,
            );
        }

        if ($this->pubSub instanceof ConnectionFactory) {
            $this->pubSub = new PubSub($this->pubSub->connect());
        }

        return new ResourceManager($this->pubSub, $this->resourceSerializer);
    }

    public function getResourceVersionManager(): ResourceVersionManagerInterface
    {
        return new ResourceVersionManager($this->httpClient, $this->requestFactory);
    }
}
