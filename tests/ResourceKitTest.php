<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests;

use Cerpus\EdlibResourceKit\ResourceKit;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Cerpus\PubSub\Connection\ConnectionFactory;
use Cerpus\PubSub\PubSub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class ResourceKitTest extends TestCase
{
    /**
     * @dataProvider resourceKitConstructorArgsProvider
     */
    public function testGetResourceManager(array $args): void
    {
        $this->expectNotToPerformAssertions();

        $resourceKit = new ResourceKit(...$args);

        $resourceKit->getResourceManager();
    }

    /**
     * @dataProvider resourceKitConstructorArgsProvider
     */
    public function testGetResourceVersionManager(array $args): void
    {
        $this->expectNotToPerformAssertions();

        $resourceKit = new ResourceKit(...$args);

        $resourceKit->getResourceVersionManager();
    }

    public function resourceKitConstructorArgsProvider(): iterable
    {
        $pubSub = $this->createMock(PubSub::class);
        $connectionFactory = $this->createMock(ConnectionFactory::class);
        $httpClient = $this->createMock(ClientInterface::class);
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $resourceSerializer = $this->createMock(ResourceSerializer::class);

        yield [[$pubSub]];
        yield [[$connectionFactory]];
        yield [[$pubSub, 'httpClient' => $httpClient]];
        yield [[$pubSub, 'requestFactory' => $requestFactory]];
        yield [[$pubSub, 'resourceSerializer' => $resourceSerializer]];
        yield [[$pubSub, $httpClient, $requestFactory, $resourceSerializer]];
        yield [[
            'resourceSerializer' => $resourceSerializer,
            'requestFactory' => $requestFactory,
            'httpClient' => $httpClient,
            'pubSub' => $pubSub,
        ]];
    }
}
