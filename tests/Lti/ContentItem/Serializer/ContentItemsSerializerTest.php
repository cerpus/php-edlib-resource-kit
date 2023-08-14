<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\ContentItemsMapper;
use Cerpus\EdlibResourceKit\Lti\ContentItem\PresentationDocumentTarget;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer\ContentItemsSerializer;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

final class ContentItemsSerializerTest extends TestCase
{
    public function testSerializesTheExampleFromReadme(): void
    {
        $items = new ContentItems([
            new LtiLinkItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                title: 'My Cool LTI Content',
                url: 'https://example.com/my-lti-content',
            ),
        ]);

        $serializer = new ContentItemsSerializer();
        $serialized = $serializer->serialize($items);

        $this->assertEquals([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'title' => 'My Cool LTI Content',
                    'url' => 'https://example.com/my-lti-content'
                ]
            ],
        ], $serialized);
    }

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
            new FileItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                title: 'My Other Cool Content',
                copyAdvice: true,
                expiresAt: new DateTimeImmutable('2020-01-01T00:00:00Z'),
            )
        ]);

        $serialized = (new ContentItemsSerializer())->serialize($contentItems);

        $this->assertEquals([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'icon' => [
                        '@id' => 'http://example.com/icon.jpg',
                        'width' => 320,
                        'height' => 240,
                    ],
                    'thumbnail' => [
                        '@id' => 'http://example.com/thumbnail.jpg',
                        'width' => 32,
                        'height' => 24,
                    ],
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'placementAdvice' => [
                        'displayWidth' => 640,
                        'displayHeight' => 480,
                        'presentationDocumentTarget' => 'iframe',
                        'windowTarget' => '_top',
                    ],
                    'title' => 'My Cool Content',
                    'text' => 'A cool text description of my cool content',
                    'url' => 'https://example.com/lti',
                ],
                [
                    '@type' => 'FileItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'title' => 'My Other Cool Content',
                    'expiresAt' => '2020-01-01T00:00:00+00:00',
                    'copyAdvice' => true,
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
