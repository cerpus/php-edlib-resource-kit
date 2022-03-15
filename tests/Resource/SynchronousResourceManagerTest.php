<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Resource;

use ArrayObject;
use Cerpus\EdlibResourceKit\Resource\Exception\ResourceSaveFailedException;
use Cerpus\EdlibResourceKit\Resource\SynchronousResourceManager;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Cerpus\EdlibResourceKit\Tests\Contract\EdlibResourceStub;
use Cerpus\EdlibResourceKit\Util\Json;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;

final class SynchronousResourceManagerTest extends TestCase
{
    private ArrayObject $history;
    private HttpFactory $httpFactory;
    private MockHandler $mockedResponses;
    private ResourceSerializer $resourceSerializer;
    private SynchronousResourceManager $resourceManager;

    protected function setUp(): void
    {
        $this->history = new ArrayObject();
        $this->mockedResponses = new MockHandler();

        $handler = HandlerStack::create($this->mockedResponses);
        $handler->push(Middleware::history($this->history));

        $client = new Client(['handler' => $handler]);
        $this->httpFactory = new HttpFactory();

        $this->resourceSerializer = new ResourceSerializer();
        $this->resourceManager = new SynchronousResourceManager(
            $client,
            $this->httpFactory,
            $this->resourceSerializer,
            $this->httpFactory,
        );
    }

    public function testSave(): void
    {
        $resource = new EdlibResourceStub();
        $this->mockedResponses->append($this->httpFactory->createResponse(201));

        $this->resourceManager->save($resource);

        $this->assertCount(1, $this->history);
        $this->assertSame(
            'http://resourceapi/v1/resources',
            (string) $this->history[0]['request']->getUri(),
        );
        $this->assertSame(
            Json::encode($this->resourceSerializer->serialize(new EdlibResourceStub())),
            $this->history[0]['request']->getBody()->getContents(),
        );
    }

    public function testSaveWithInvalidHttpStatusCode(): void
    {
        $resource = new EdlibResourceStub();
        $this->mockedResponses->append($this->httpFactory->createResponse(404));

        $this->expectException(ResourceSaveFailedException::class);

        $this->resourceManager->save($resource);
    }

    public function testSaveWithClientFailure(): void
    {
        $resource = new EdlibResourceStub();
        $this->mockedResponses->append(new TransferException());

        $this->expectException(ResourceSaveFailedException::class);

        $this->resourceManager->save($resource);
    }
}
