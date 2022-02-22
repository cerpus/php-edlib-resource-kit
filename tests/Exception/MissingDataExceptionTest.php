<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Exception;

use Cerpus\EdlibResourceKit\Exception\MissingDataException;
use PHPUnit\Framework\TestCase;

final class MissingDataExceptionTest extends TestCase
{
    public function testMissingKey(): void
    {
        $this->expectExceptionMessage('Missing key "foo"');

        throw MissingDataException::missingKey('foo');
    }
}
