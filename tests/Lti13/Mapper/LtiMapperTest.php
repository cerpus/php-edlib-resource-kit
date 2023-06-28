<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti13\Mapper;

use Cerpus\EdlibResourceKit\Lti13\Mapper\LtiMapper;
use PHPUnit\Framework\TestCase;

final class LtiMapperTest extends TestCase
{
    private LtiMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new LtiMapper();
    }

    public function testMapsTheStuff(): void
    {
        // http://www.imsglobal.org/spec/lti/v1p3/#examplelinkrequest
        $message = $this->mapper->map(json_decode(<<<EODATA
        {
            "aud" : [
                "962fa4d8-bcbf-49a0-94b2-2de05ad274af"
            ],
            "azp" : "962fa4d8-bcbf-49a0-94b2-2de05ad274af",
            "email" : "jane@platform.example.edu",
            "exp" : 1510185728,
            "family_name" : "Doe",
            "given_name" : "Jane",
            "http://www.ExamplePlatformVendor.com/session" : {
                "id" : "89023sj890dju080"
            },
            "https://purl.imsglobal.org/spec/lti/claim/context" : {
                "id" : "c1d887f0-a1a3-4bca-ae25-c375edcc131a",
                "label" : "ECON 1010",
                "title" : "Economics as a Social Science",
                "type" : [
                    "http://purl.imsglobal.org/vocab/lis/v2/course#CourseOffering"
                ]
            },
            "https://purl.imsglobal.org/spec/lti/claim/custom" : {
                "request_url" : "https://tool.com/link/123",
                "xstart" : "2017-04-21T01:00:00Z"
            },
            "https://purl.imsglobal.org/spec/lti/claim/deployment_id" : "07940580-b309-415e-a37c-914d387c1150",
            "https://purl.imsglobal.org/spec/lti/claim/launch_presentation" : {
                "document_target" : "iframe",
                "height" : 320,
                "return_url" : "https://platform.example.edu/terms/201601/courses/7/sections/1/resources/2",
                "width" : 240
            },
            "https://purl.imsglobal.org/spec/lti/claim/lis" : {
                "course_offering_sourcedid" : "example.edu:SI182-F16",
                "course_section_sourcedid" : "example.edu:SI182-001-F16",
                "person_sourcedid" : "example.edu:71ee7e42-f6d2-414a-80db-b69ac2defd4"
            },
            "https://purl.imsglobal.org/spec/lti/claim/message_type" : "LtiResourceLinkRequest",
            "https://purl.imsglobal.org/spec/lti/claim/resource_link" : {
                "description" : "Assignment to introduce who you are",
                "id" : "200d101f-2c14-434a-a0f3-57c2a42369fd",
                "title" : "Introduction Assignment"
            },
            "https://purl.imsglobal.org/spec/lti/claim/role_scope_mentor" : [
                "fad5fb29-a91c-770-3c110-1e687120efd9",
                "5d7373de-c76c-e2b-01214-69e487e2bd33",
                "d779cfd4-bc7b-019-9bf1a-04bf1915d4d0"
            ],
            "https://purl.imsglobal.org/spec/lti/claim/roles" : [
                "http://purl.imsglobal.org/vocab/lis/v2/institution/person#Student",
                "http://purl.imsglobal.org/vocab/lis/v2/membership#Learner",
                "http://purl.imsglobal.org/vocab/lis/v2/membership#Mentor"
            ],
            "https://purl.imsglobal.org/spec/lti/claim/target_link_uri" : "https://tool.example.com/lti/48320/ruix8782rs",
            "https://purl.imsglobal.org/spec/lti/claim/tool_platform" : {
                "contact_email" : "support@platform.example.edu",
                "description" : "An Example Tool Platform",
                "guid" : "ex/48bbb541-ce55-456e-8b7d-ebc59a38d435",
                "name" : "Example Tool Platform",
                "product_family_code" : "ExamplePlatformVendor-Product",
                "url" : "https://platform.example.edu",
                "version" : "1.0"
            },
            "https://purl.imsglobal.org/spec/lti/claim/version" : "1.3.0",
            "iat" : 1510185228,
            "iss" : "https://platform.example.edu",
            "locale" : "en-US",
            "middle_name" : "Marie",
            "name" : "Ms Jane Marie Doe",
            "nonce" : "fc5fdc6d-5dd6-47f4-b2c9-5d1216e9b771",
            "picture" : "https://platform.example.edu/jane.jpg",
            "sub" : "a6d5c443-1f51-4783-ba1a-7686ffe3b54a"
        }
        EODATA));

        $this->assertSame('a6d5c443-1f51-4783-ba1a-7686ffe3b54a', $message->getSubject());
    }
}
