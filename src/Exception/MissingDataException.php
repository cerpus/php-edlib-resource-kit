<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Exception;

class MissingDataException extends RuntimeException implements ExceptionInterface
{
    public static function missingKey(string $key): self
    {
        return new self('Missing key "' . $key . '"');
    }
}
