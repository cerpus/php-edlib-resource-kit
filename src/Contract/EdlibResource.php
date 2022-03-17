<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Contract;

use DateTimeImmutable;

interface EdlibResource
{
    /**
     * The name of the system managing this resource. This must be unique and
     * consistent, and URL-safe.
     */
    public function getExternalSystemName(): string;

    /**
     * The ID of the resource inside the system. This must be URL-safe.
     */
    public function getExternalSystemId(): string;

    /**
     * The title of the resource.
     */
    public function getTitle(): string;

    /**
     * Get the ID of the resource owner. The ID is managed by the external
     * system.
     */
    public function getOwnerId(): string|null;

    public function isPublished(): bool;

    public function isListed(): bool;

    public function isDraft(): bool;

    /**
     * The language as an ISO 639-3 identifier, or NULL if no language is
     * defined.
     */
    public function getLanguage(): string|null;

    public function getContentType(): string|null;

    public function getLicense(): string|null;

    /**
     * The maximum score attainable, or NULL if the resource does not have a
     * scoring mechanism.
     */
    public function getMaxScore(): int|float|null;

    /**
     * The time the resource was created.
     */
    public function getCreatedAt(): DateTimeImmutable;

    /**
     * The time the resource was last modified.
     */
    public function getUpdatedAt(): DateTimeImmutable;

    /**
     * Get the list of collaborator user IDs. These are managed by Edlib
     * @return array<string>
     */
    public function getCollaborators(): array;

    /**
     * Get the list of email addresses added as collaborators.
     * @return array<string>
     */
    public function getEmailCollaborators(): array;
}
