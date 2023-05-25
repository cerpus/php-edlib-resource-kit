<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

/**
 * How to read a field's value.
 */
class Reader
{
    public function __construct(
        private readonly string $name,
        private readonly ReaderType $type,
        private readonly bool $private = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ReaderType
    {
        return $this->type;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }
}
