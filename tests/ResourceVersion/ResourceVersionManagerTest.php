<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\ResourceVersion;

use Cerpus\EdlibResourceKit\ResourceVersion\ResourceVersionManager;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

final class ResourceVersionManagerTest extends TestCase
{
    /** @var ClientInterface&MockObject  */
    private ClientInterface $client;

    private HttpFactory $httpFactory;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->httpFactory = new HttpFactory();
        $this->manager = new ResourceVersionManager(
            $this->client,
            $this->httpFactory,
        );
    }

    public function testGetCurrentVersion(): void
    {
        $resourceId = '123';
        $data = [
            'id' => '123',
            'resourceId' => '345',
            'externalSystemName' => 'test',
            'externalSystemId' => '1234',
            'title' => 'My resource',
            'description' => 'My description',
            'isPublished' => true,
            'isListed' => false,
            'language' => 'eng',
            'contentType' => 'h5p.draganddrop',
            'license' => 'cc0',
            'ownerId' => '567',
            'createdAt' => '2022-01-01T00:00:00Z',
            'updatedAt' => '2023-01-01T00:00:00Z',
            'maxScore' => null,
            'isDraft' => false,
        ];

        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(fn(RequestInterface $request) =>
                $request->getMethod() === 'GET' &&
                $request->getUri()->__toString() === 'http://resourceapi/v1/resources/'.$resourceId.'/version'),
            )
            ->willReturn(
                $this->httpFactory->createResponse()->withBody(
                    $this->httpFactory->createStream(json_encode($data)),
                ),
            );

        $this->manager->getCurrentVersion($resourceId);
    }
}
