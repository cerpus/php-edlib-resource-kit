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
use Cerpus\EdlibResourceKit\Tests\Stub\Oauth1\InMemoryCredentialStore;
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
    private InMemoryCredentialStore $credentials;

    private Validator $validator;

    protected function setUp(): void
    {
        $clock = new MockClock('@1000000000');

        $this->credentials = new InMemoryCredentialStore();

        $this->validator = new Validator(
            $this->credentials,
            new Signer($clock, new Randomizer(new RandomEngineStub())),
            new ArrayCachePool(),
            $clock,
        );
    }

    #[DoesNotPerformAssertions]
    public function testValidRequestPasses(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->validator->validate($request);
    }

    public function testMissingConsumerKeyIsRejected(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('No consumer key provided'),
        );

        $this->validator->validate($request);
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

        $this->validator->validate($request);
    }

    public function testMissingNonceIsRejected(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_signature' => 'YMMkbRwzQgrchvOiwY7k4/4Pq1Y=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(new ValidationException('No nonce provided'));

        $this->validator->validate($request);
    }

    public function testNoncesCannotBeReused(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => '6w+On/hrM4ijTwIQDyCylJv3sUE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->validator->validate($request);

        $this->expectExceptionObject(
            new ValidationException('Provided nonce has already been used'),
        );

        $this->validator->validate($request);
    }

    public function testSignatureIsRequired(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('No signature provided'),
        );

        $this->validator->validate($request);
    }

    public function testInvalidSignaturesAreRejected(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaa=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('Provided signature does not match'),
        );

        $this->validator->validate($request);
    }

    public function testRequiresHmacSha1Signature(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'BIlPuLKBDvvgvXSz8ur0FVIETgY=',
            'oauth_signature_method' => 'HMAC-SHA2',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('Signature method must be "HMAC-SHA1"'),
        );

        $this->validator->validate($request);
    }

    public function testTimestampIsRequired(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'wpJ918w6kKO/gJ4a6SwXmBOZ4jE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('No timestamp provided'),
        );

        $this->validator->validate($request);
    }

    public function testTimestampPastAllowedLeewayIsRejected(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'OY9dl0+fRKdqwsx04JVhKU9b3rE=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000301',
            'oauth_version' => '1.0',
        ]);

        $this->expectExceptionObject(
            new ValidationException('Provided time deviates too much from server time'),
        );

        $this->validator->validate($request);
    }

    public function testVersionMustBeOnePointZero(): void
    {
        $this->credentials->add(new Credentials('my-client', 'my-secret'));

        $request = new Request('POST', 'https://example.com/', [
            'oauth_consumer_key' => 'my-client',
            'oauth_nonce' => 'NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0NDQ0',
            'oauth_signature' => 'vGxugGVDGSOwpDxmkWXbuCi+EwI=',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1000000000',
            'oauth_version' => '1.1',
        ]);

        $this->expectExceptionObject(
            new ValidationException('Provided version must be "1.0" or omitted'),
        );

        $this->validator->validate($request);
    }
}
