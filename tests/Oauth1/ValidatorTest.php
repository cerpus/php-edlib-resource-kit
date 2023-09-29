<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Oauth1;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Cerpus\EdlibResourceKit\Oauth1\Credentials;
use Cerpus\EdlibResourceKit\Oauth1\Exception\ValidationException;
use Cerpus\EdlibResourceKit\Oauth1\Request;
use Cerpus\EdlibResourceKit\Oauth1\Signer;
use Cerpus\EdlibResourceKit\Oauth1\Validator;
use Cerpus\EdlibResourceKit\Tests\Stub\RandomEngineStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Random\Randomizer;
use Symfony\Component\Clock\MockClock;

#[CoversClass(Signer::class)]
#[CoversClass(Validator::class)]
#[CoversClass(Request::class)]
final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $clock = new MockClock('@1000000000');

        $this->validator = new Validator(
            new Signer($clock, new Randomizer(new RandomEngineStub())),
            new ArrayCachePool(),
            $clock,
        );
    }

    #[DoesNotPerformAssertions]
    public function testValidRequestPasses(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->validator->validate($request, $credentials);
    }

    public function testMissingConsumerKeyIsRejected(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('No consumer key provided'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testMustMatchKeyOfExistingConsumer(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('Provided key does not correspond to any known consumer'),
        );

        $this->validator->validate($request, new Credentials('foo', 'bar'));
    }

    public function testMissingNonceIsRejected(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_signature' => 'YMMkbRwzQgrchvOiwY7k4/4Pq1Y=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(new ValidationException('No nonce provided'));

        $this->validator->validate($request, $credentials);
    }

    public function testNoncesCannotBeReused(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->validator->validate($request, $credentials);

        $this->expectExceptionObject(
            new ValidationException('Provided nonce has already been used'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testSignatureIsRequired(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('No signature provided'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testInvalidSignaturesAreRejected(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaa=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('Provided signature does not match'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testRequiresHmacSha1Signature(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'BIlPuLKBDvvgvXSz8ur0FVIETgY=',
            'oauth_signature_method' => 'HMAC-SHA2',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('Signature method must be "HMAC-SHA1"'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testTimestampIsRequired(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'wpJ918w6kKO/gJ4a6SwXmBOZ4jE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('No timestamp provided'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testTimestampPastAllowedLeewayIsRejected(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'OY9dl0+fRKdqwsx04JVhKU9b3rE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000301',
            'oauth_version' => '1.0',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('Provided time deviates too much from server time'),
        );

        $this->validator->validate($request, $credentials);
    }

    public function testVersionMustBeOnePointZero(): void
    {
        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'vGxugGVDGSOwpDxmkWXbuCi+EwI=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.1',
        ]);
        $credentials = new Credentials('my-client', 'my-secret');

        $this->expectExceptionObject(
            new ValidationException('Provided version must be "1.0" or omitted'),
        );

        $this->validator->validate($request, $credentials);
    }
}
