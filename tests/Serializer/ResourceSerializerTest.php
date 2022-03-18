<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Serializer;

use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;
use Cerpus\EdlibResourceKit\Tests\Contract\DraftAwareResourceStub;
use Cerpus\EdlibResourceKit\Tests\Contract\EdlibResourceStub;
use PHPUnit\Framework\TestCase;

final class ResourceSerializerTest extends TestCase
{
    private ResourceSerializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new ResourceSerializer();
    }

    public function testSerializeWithoutIsDraft(): void
    {
        $resource = new EdlibResourceStub();

        $this->assertEquals([
            'externalSystemName' => $resource->getExternalSystemName(),
            'externalSystemId' => $resource->getExternalSystemId(),
            'title' => $resource->getTitle(),
            'ownerId' => $resource->getOwnerId(),
            'isPublished' => $resource->isPublished(),
            'isListed' => $resource->isListed(),
            'isDraft' => false,
            'createdAt' => $resource->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $resource->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'maxScore' => $resource->getMaxScore(),
            'language' => $resource->getLanguage(),
            'contentType' => $resource->getContentType(),
            'license' => $resource->getLicense(),
            'collaborators' => $resource->getCollaborators(),
            'emailCollaborators' => $resource->getEmailCollaborators(),
        ], $this->serializer->serialize($resource));
    }

    public function testSerializeWithIsDraft(): void
    {
        $resource = new DraftAwareResourceStub();

        $this->assertEquals([
            'externalSystemName' => $resource->getExternalSystemName(),
            'externalSystemId' => $resource->getExternalSystemId(),
            'title' => $resource->getTitle(),
            'ownerId' => $resource->getOwnerId(),
            'isPublished' => $resource->isPublished(),
            'isListed' => $resource->isListed(),
            'isDraft' => $resource->isDraft(),
            'createdAt' => $resource->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $resource->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'maxScore' => $resource->getMaxScore(),
            'language' => $resource->getLanguage(),
            'contentType' => $resource->getContentType(),
            'license' => $resource->getLicense(),
            'collaborators' => $resource->getCollaborators(),
            'emailCollaborators' => $resource->getEmailCollaborators(),
        ], $this->serializer->serialize($resource));
    }
}
