<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

interface MappingInterface
{
    /**
     * @return Field[]
     */
    public function getFields(object|string $objectOrClass): array;

    /**
     * Get the ID of a JSON schema that validates input for an object,
     */
    public function getJsonSchemaId(object|string $objectOrClass): string|null;
}
