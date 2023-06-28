<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Oauth1;

use InvalidArgumentException;
use SensitiveParameter;

final readonly class Credentials
{
    public string $secret;

    public function __construct(
        public string $key,
        #[SensitiveParameter] string $secret,
    ) {
        if ($key === '') {
            throw new InvalidArgumentException('Key cannot be empty');
        }

        if ($secret === '') {
            throw new InvalidArgumentException('Secret cannot be empty');
        }

        $this->secret = $secret;
    }
}
