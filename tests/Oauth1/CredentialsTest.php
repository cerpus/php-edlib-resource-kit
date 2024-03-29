<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Oauth1;

use Cerpus\EdlibResourceKit\Oauth1\Credentials;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Credentials::class)]
final class CredentialsTest extends TestCase
{
    public function testCannotUseEmptyKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Credentials('', 'not empty');
    }

    public function testCannotUseEmptySecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Credentials('not empty', '');
    }

    public function testFindsItselfAsSingleKeyCredentialsStore(): void
    {
        $credentials = new Credentials('foo', 'bar');

        $this->assertSame($credentials, $credentials->findByKey('foo'));
    }

    public function testDoesNotFindItselfWhenLookingUpOtherKeys(): void
    {
        $credentials = new Credentials('foo', 'bar');

        $this->assertNull($credentials->findByKey('boo'));
    }
}
