<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\ResourceVersion;

use Cerpus\EdlibResourceKit\Util\Json;
use DateTimeImmutable;
use Cerpus\EdlibResourceKit\Exception\HttpException;
use Cerpus\EdlibResourceKit\Exception\MissingDataException;
use Cerpus\EdlibResourceKit\Exception\ResourceNotFoundException;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

final class ResourceVersionManager implements ResourceVersionManagerInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @throws ResourceNotFoundException if the resource does not exist
     */
    public function getCurrentVersion(string $resourceId): ResourceVersion
    {
        if ($resourceId === '') {
            throw new InvalidArgumentException('$resourceId must not be empty');
        }

        $data = $this->jsonRequest($this->requestFactory->createRequest(
            'GET',
            'http://resourceapi/v1/resources/'.$resourceId.'/version',
        ));

        return self::map($data);
    }

    /**
     * @throws ResourceNotFoundException if the resource or version does not exist
     */
    public function getVersion(
        string $resourceId,
        string $versionId,
    ): ResourceVersion {
        if ($resourceId === '') {
            throw new InvalidArgumentException('$resourceId must not be empty');
        }

        if ($versionId === '') {
            throw new InvalidArgumentException('$versionId must not be empty');
        }

        $request = $this->requestFactory->createRequest(
            'GET',
            'http://resourceapi/v1/resources/'.$resourceId.'/versions/'.$versionId,
        );

        $data = $this->jsonRequest($request);

        return self::map($data);
    }

    /**
     * @throws ResourceNotFoundException if the resource does not exist
     * @throws HttpException
     */
    private function jsonRequest(RequestInterface $request): array
    {
        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        $statusCode = $response->getStatusCode();

        if ($statusCode === 404) {
            throw new ResourceNotFoundException();
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new HttpException('Invalid HTTP status code', $statusCode ?? 0);
        }

        return Json::decode($response->getBody()->getContents());
    }

    private static function map(array $data): ResourceVersion
    {
        $extract = function (string $key) use (&$data): mixed {
            $value = array_key_exists($key, $data)
                ? $data[$key]
                : throw MissingDataException::missingKey($key);
            unset($data[$key]);

            return $value;
        };

        return new ResourceVersion(
            $extract('id'),
            $extract('resourceId'),
            $extract('externalSystemName'),
            $extract('externalSystemId'),
            $extract('title'),
            $extract('description'),
            $extract('isPublished'),
            $extract('isListed'),
            $extract('language'),
            $extract('contentType'),
            $extract('license'),
            $extract('ownerId'),
            new DateTimeImmutable($extract('createdAt')),
            new DateTimeImmutable($extract('updatedAt')),
            $extract('maxScore'),
            $extract('isDraft'),
            $data,
        );
    }
}
