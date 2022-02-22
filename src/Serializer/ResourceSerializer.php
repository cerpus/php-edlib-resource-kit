<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Serializer;

use DateTimeInterface;
use Cerpus\EdlibResourceKit\Contract\EdlibResource;

/**
 * Serializes a resource for sending on the message bus.
 */
class ResourceSerializer
{
    public function serialize(EdlibResource $resource): array
    {
        return [
            'externalSystemName' => $resource->getExternalSystemName(),
            'externalSystemId' => $resource->getExternalSystemId(),
            'title' => $resource->getTitle(),
            'ownerId' => $resource->getOwnerId(),
            'isPublished' => $resource->isPublished(),
            'isListed' => $resource->isListed(),
            'language' => $resource->getLanguage(),
            'contentType' => $resource->getContentType(),
            'license' => $resource->getLicense(),
            'maxScore' => $resource->getMaxScore(),
            'createdAt' => $resource->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $resource->getUpdatedAt()->format(DateTimeInterface::ATOM),
            'collaborators' => $resource->getCollaborators(),
            'emailCollaborators' => $resource->getEmailCollaborators(),
        ];
    }
}
