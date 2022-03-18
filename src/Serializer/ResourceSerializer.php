<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Serializer;

use Cerpus\EdlibResourceKit\Contract\DraftAwareResource;
use DateTimeInterface;
use Cerpus\EdlibResourceKit\Contract\EdlibResource;

/**
 * Serializes a resource for publication.
 */
class ResourceSerializer
{
    public function serialize(EdlibResource $resource): array
    {
        $data = [
            'externalSystemName' => $resource->getExternalSystemName(),
            'externalSystemId' => $resource->getExternalSystemId(),
            'title' => $resource->getTitle(),
            'ownerId' => $resource->getOwnerId(),
            'isPublished' => $resource->isPublished(),
            'isListed' => $resource->isListed(),
            'isDraft' => false,
            'language' => $resource->getLanguage(),
            'contentType' => $resource->getContentType(),
            'license' => $resource->getLicense(),
            'maxScore' => $resource->getMaxScore(),
            'createdAt' => $resource->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $resource->getUpdatedAt()->format(DateTimeInterface::ATOM),
            'collaborators' => $resource->getCollaborators(),
            'emailCollaborators' => $resource->getEmailCollaborators(),
        ];

        if ($resource instanceof DraftAwareResource) {
            $data['isDraft'] = $resource->isDraft();
        }

        return $data;
    }
}
