<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource;

use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\Resource\Exception\ResourceSaveFailedException;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Cerpus\EdlibResourceKit\Util\Json;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function sprintf;

final class SynchronousResourceManager implements ResourceManagerInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private ResourceSerializer $resourceSerializer,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function save(EdlibResource $resource): void
    {
        $request = $this->requestFactory
            ->createRequest('POST', 'http://resourceapi/v1/resources')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(
                Json::encode($this->resourceSerializer->serialize($resource)),
            ));

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ResourceSaveFailedException(previous: $e);
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new ResourceSaveFailedException(sprintf(
                'Unexpected HTTP status code (%d)',
                $response->getStatusCode(),
            ));
        }
    }
}
