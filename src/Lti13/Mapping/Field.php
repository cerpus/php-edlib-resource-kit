<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

class Field
{
    public function __construct(
        private readonly string $claim,
        private readonly Reader $reader,
    ) {
    }

    public function getClaim(): string
    {
        return $this->claim;
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    public function withReader(Reader $reader): self
    {
        return new self($this->claim, $reader);
    }
}
