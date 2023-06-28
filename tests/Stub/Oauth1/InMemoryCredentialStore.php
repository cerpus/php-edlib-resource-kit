<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Stub\Oauth1;

use Cerpus\EdlibResourceKit\Oauth1\Credentials;
use Cerpus\EdlibResourceKit\Oauth1\CredentialStoreInterface;

class InMemoryCredentialStore implements CredentialStoreInterface
{
    /**
     * @var array<string, Credentials>
     */
    private array $keyCredentialsMap = [];

    public function findByKey(string $key): Credentials|null
    {
        return $this->keyCredentialsMap[$key] ?? null;
    }

    public function add(Credentials $credentials): void
    {
        $this->keyCredentialsMap[$credentials->key] = $credentials;
    }
}
