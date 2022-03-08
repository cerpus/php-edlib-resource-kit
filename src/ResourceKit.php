<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit;

use Cerpus\PubSub\Connection\ConnectionFactory;
use Cerpus\PubSub\PubSub;
use Cerpus\EdlibResourceKit\Resource\ResourceManager;
use Cerpus\EdlibResourceKit\Resource\ResourceManagerInterface;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManager;
use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManagerInterface;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class ResourceKit implements ResourceKitInterface
{
    private PubSub|ConnectionFactory $pubSub;
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private ResourceSerializer $resourceSerializer;

    public function __construct(
        PubSub|ConnectionFactory $pubSub,
        ClientInterface|null $httpClient = null,
        RequestFactoryInterface|null $requestFactory = null,
        ResourceSerializer $resourceSerializer = null,
    ) {
        $this->pubSub = $pubSub;
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->resourceSerializer = $resourceSerializer ?? new ResourceSerializer();
    }

    public function getResourceManager(): ResourceManagerInterface
    {
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
