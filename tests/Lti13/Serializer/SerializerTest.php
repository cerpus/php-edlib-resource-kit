<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti13\Serializer;

use Cerpus\EdlibResourceKit\Lti13\Attribute\Claim;
use Cerpus\EdlibResourceKit\Lti13\LtiMessage;
use Cerpus\EdlibResourceKit\Lti13\LtiResourceLinkRequest;
use Cerpus\EdlibResourceKit\Lti13\ResourceLink;
use Cerpus\EdlibResourceKit\Lti13\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

final class SerializerTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer();
    }

    public function testSerializesLtiMessages(): void
    {
        $message = new LtiResourceLinkRequest(
            resourceLink: new ResourceLink(
                id: '200d101f-2c14-434a-a0f3-57c2a42369fd',
                description: 'Some description',
                title: 'Some title',
            ),
            deploymentId: 'my-deployment-id',
            targetLinkUri: 'https://example.com/target-url',
            subject: 'user-323',
            givenName: 'Donald',
            familyName: 'Duck',
            locale: 'nb',
            email: 'donald@disney.com',
        );

        $serialized = $this->serializer->serialize($message);

        $this->assertEquals([
            'https://purl.imsglobal.org/spec/lti/claim/message_type' => 'LtiResourceLinkRequest',
            'https://purl.imsglobal.org/spec/lti/claim/version' => '1.3.0',
            'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => 'my-deployment-id',
            'https://purl.imsglobal.org/spec/lti/claim/roles' => [],
            'https://purl.imsglobal.org/spec/lti/claim/resource_link' => [
                'id' => '200d101f-2c14-434a-a0f3-57c2a42369fd',
                'description' => 'Some description',
                'title' => 'Some title',
            ],
            'https://purl.imsglobal.org/spec/lti/claim/target_link_uri' => 'https://example.com/target-url',
            'sub' => 'user-323',
            'given_name' => 'Donald',
            'family_name' => 'Duck',
            'locale' => 'nb',
            'email' => 'donald@disney.com',
        ], $serialized);
    }

    public function testSerializesCustomAttributesInLtiMessages(): void
    {
        $message = new class('my-deployment-id') extends LtiMessage {
            #[Claim]
            public const UNPREFIXED_CONSTANT_CLAIM = 'unprefixed constant claim';

            #[Claim('https://example.com/constant-claim')]
            public const CONSTANT_CLAIM = 'constant claim';

            #[Claim]
            public $unprefixedPropertyClaim = 'unprefixed property claim';

            #[Claim('https://example.com/property-claim')]
            public $propertyClaim = 'property claim';

            public function getMessageType(): string
            {
                return 'MyCustomMessage';
            }

            #[Claim('https://example.com/method-claim')]
            public function getMethodClaim(): string
            {
                return 'method claim';
            }

            #[Claim]
            public function getUnprefixedMethodClaim(): string
            {
                return 'unprefixed method claim';
            }
        };

        $serialized = $this->serializer->serialize($message);

        $this->assertEquals([
            'https://purl.imsglobal.org/spec/lti/claim/deployment_id' => 'my-deployment-id',
            'https://purl.imsglobal.org/spec/lti/claim/message_type' => 'MyCustomMessage',
            'https://purl.imsglobal.org/spec/lti/claim/version' => '1.3.0',
            'unprefixed_constant_claim' => 'unprefixed constant claim',
            'https://example.com/constant-claim' => 'constant claim',
            'unprefixed_property_claim' => 'unprefixed property claim',
            'https://example.com/property-claim' => 'property claim',
            'unprefixed_method_claim' => 'unprefixed method claim',
            'https://example.com/method-claim' => 'method claim',
        ], $serialized);
    }
}
