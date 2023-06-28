<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Oauth1;

use Cerpus\EdlibResourceKit\Oauth1\Exception\ValidationException;

interface ValidatorInterface
{
    /**
     * Ensure an OAuth1 request is well-formed and authenticated.
     * @throws ValidationException
     */
    public function validate(Request $request): void;
}
