<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\Lti11\Serializer;

use Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking\ContentItemsMapper;
use Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking\ContentItemsSerializer;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\FileItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\Image;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LineItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\PresentationDocumentTarget;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

final class ContentItemsSerializerTest extends TestCase
{
    private ContentItemsSerializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new ContentItemsSerializer();
    }

    public function testSerializesTheExampleFromReadme(): void
    {
        $items = [
            new LtiLinkItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                title: 'My Cool LTI Content',
                url: 'https://example.com/my-lti-content',
            ),
        ];

        $serialized = $this->serializer->serialize($items);

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
        $contentItems = [
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
                custom: [
                    'level' => 'expert',
                    'numericLevel' => 42,
                ],
                lineItem: new LineItem(
                    new ScoreConstraints(39.5, 2.5)
                ),
            ),
            new FileItem(
                mediaType: 'application/vnd.ims.lti.v1.ltilink',
                title: 'My Other Cool Content',
                copyAdvice: true,
                expiresAt: new DateTimeImmutable('2020-01-01T00:00:00Z'),
            )
        ];

        $serialized = $this->serializer->serialize($contentItems);

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
                    'lineItem' => [
                        '@type' => 'LineItem',
                        'scoreConstraints' => [
                            '@type' => 'NumericLimits',
                            'normalMaximum' => 39.5,
                            'extraCreditMaximum' => 2.5,
                            'totalMaximum' => 42.0,
                        ],
                    ],
                    'custom' => [
                        'level' => 'expert',
                        'numericLevel' => 42,
                    ],
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
        $items = (new ContentItemsMapper())->map($serialized);

        $this->assertCount(2, $items);
    }
}
