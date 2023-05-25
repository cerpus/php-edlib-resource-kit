<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

interface MappingInterface
{
    /**
     * @return Field[]
     */
    public function getFields(object $object): array;
}
