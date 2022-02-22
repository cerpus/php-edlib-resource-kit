<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Resource;

use Cerpus\PubSub\PubSub;
use Cerpus\EdlibResourceKit\Resource\ResourceManager;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Cerpus\EdlibResourceKit\Tests\Contracts\EdlibResourceStub;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResourceManagerTest extends TestCase
{
    /** @var PubSub&MockObject  */
    private PubSub $pubSub;

    /** @var ResourceSerializer&MockObject */
    private ResourceSerializer $resourceSerializer;

    protected function setUp(): void
    {
        $this->pubSub = $this->createMock(PubSub::class);
        $this->resourceSerializer = $this->getMockBuilder(ResourceSerializer::class)
            ->enableProxyingToOriginalMethods()
            ->getMock();
        $this->resourceManager = new ResourceManager(
            $this->pubSub,
            $this->resourceSerializer,
        );
    }

    public function testSave(): void
    {
        $resource = new EdlibResourceStub();

        $serialized = json_encode(
            $this->resourceSerializer->serialize($resource),
            JSON_THROW_ON_ERROR,
        );

        $this->resourceSerializer
            ->expects($this->exactly(1))
            ->method('serialize')
            ->with($resource);

        $this->pubSub
            ->expects($this->once())
            ->method('publish')
            ->with('edlibResourceUpdate', $serialized);

        $this->resourceManager->save($resource);
    }
}
