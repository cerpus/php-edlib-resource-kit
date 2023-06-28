<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Stub;

use Random\Engine;

final readonly class RandomEngineStub implements Engine
{
    public function generate(): string
    {
        // chosen by fair dice roll.
        // guaranteed to be random.
        return '4';
    }
}
