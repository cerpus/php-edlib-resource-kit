<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Util;

use Cerpus\EdlibResourceKit\Exception\RuntimeException;
use JsonException;
use JsonSerializable;

/**
 * @internal This class should not be used outside the package
 */
class Json
{
    public static function decode(string $json): array|string|float|int|bool|null
    {
        try {
            return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Could not decode JSON', previous: $e);
        }
    }

    public static function encode(JsonSerializable|array|string|float|int|bool|null $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Could not encode JSON', previous: $e);
        }
    }
}
