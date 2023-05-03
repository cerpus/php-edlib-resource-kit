<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\ContentItemsMapper;
use Cerpus\EdlibResourceKit\Lti\ContentItem\PresentationDocumentTarget;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer\ContentItemsSerializer;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

final class ContentItemsSerializerTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function testSerializesContentItems(): array
    {
        $contentItems = new ContentItems([
            new LtiLinkItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                icon: new Image('http://example.com/icon.jpg', 320, 240),
                placementAdvice: new ContentItemPlacement(
                    displayWidth: 640,
                    displayHeight: 480,
                    presentationDocumentTarget: PresentationDocumentTarget::Iframe,
                    windowTarget: '_top',
                ),
                thumbnail: new Image('http://example.com/thumbnail.jpg', 32, 24),
                text: 'A cool text description of my cool content',
                title: 'My Cool Content',
                url: 'https://example.com/lti',
            ),
            new LtiLinkItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                title: 'My Other Cool Content',
            )
        ]);

        $serialized = (new ContentItemsSerializer())->serialize($contentItems);

        $this->assertEquals([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#icon' => [
                        '@id' => 'http://example.com/icon.jpg',
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#width' => 320,
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#height' => 240,
                    ],
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#thumbnail' => [
                        '@id' => 'http://example.com/thumbnail.jpg',
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#width' => 32,
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#height' => 24,
                    ],
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#placementAdvice' => [
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#displayWidth' => 640,
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#displayHeight' => 480,
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#presentationDocumentTarget' => 'iframe',
                        'http://purl.imsglobal.org/vocab/lti/v1/ci#windowTarget' => '_top',
                    ],
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#title' => 'My Cool Content',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#text' => 'A cool text description of my cool content',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#url' => 'https://example.com/lti',
                ],
                [
                    '@type' => 'LtiLinkItem',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'http://purl.imsglobal.org/vocab/lti/v1/ci#title' => 'My Other Cool Content',
                ],
            ],
        ], $serialized);

        return $serialized;
    }

    #[Depends('testSerializesContentItems')]
    public function testWeCanMapOurOwnSerializedData(array $serialized): void
    {
        $items = (new ContentItemsMapper())->map(json_encode($serialized));

        $this->assertCount(2, $items);
    }
}
