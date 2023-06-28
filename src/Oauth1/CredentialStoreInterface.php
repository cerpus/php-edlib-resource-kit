<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Oauth1;

interface CredentialStoreInterface
{
    public function findByKey(string $key): Credentials|null;
}
