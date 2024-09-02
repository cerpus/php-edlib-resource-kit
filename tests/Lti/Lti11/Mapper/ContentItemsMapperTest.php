<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\Lti11\Mapper;

use Cerpus\EdlibResourceKit\Lti\Exception\MappingException;
use Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking\ContentItemsMapper;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\FileItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\Image;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\PresentationDocumentTarget;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

final class ContentItemsMapperTest extends TestCase
{
    private ContentItemsMapper $mapper;

    #[Before]
    protected function setUpMapper(): void
    {
        $this->mapper = new ContentItemsMapper();
    }

    public function testItMapsAllTheStuff(): void
    {
        $contentItems = $this->mapper->map([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'title' => 'My cool content',
                    'placementAdvice' => [
                        'displayWidth' => 640,
                        'displayHeight' => 480,
                        'presentationDocumentTarget' => 'iframe',
                        'windowTarget' => '_top',
                    ],
                    'thumbnail' => [
                        '@id' => 'http://example.com/thumb',
                        'width' => 32,
                        'height' => 24,
                    ],
                    'icon' => [
                        '@id' => 'http://example.com/icon',
                        'width' => 320,
                        'height' => 240,
                    ],
                    'url' => 'http://example.com/lti',
                    'lineItem' => [
                        '@type' => 'LineItem',
                        'scoreConstraints' => [
                            '@type' => 'NumericLimits',
                            'normalMaximum' => 39.5,
                            'extraCreditMaximum' => 2.5,
                            'totalMaximum' => 42.0,
                        ],
                    ],
                ],
                [
                    '@type' => 'FileItem',
                    '@id' => 'https://example.com/image.jpeg',
                    'copyAdvice' => true,
                    'expiresAt' => '2023-05-03T00:00:00Z',
                    'mediaType' => 'image/jpeg',
                ],
                [
                    '@type' => 'ContentItem',
                    'mediaType' => 'text/html',
                    'title' => 'Some content',
                ],
            ],
        ]);

        $this->assertArrayHasKey(0, $contentItems);
        $this->assertInstanceOf(LtiLinkItem::class, $contentItems[0]);
        $this->assertSame('application/vnd.ims.lti.v1.ltilink', $contentItems[0]->getMediaType());
        $this->assertSame('My cool content', $contentItems[0]->getTitle());
        $this->assertSame('http://example.com/lti', $contentItems[0]->getUrl());
        $this->assertEquals(
            new Image('http://example.com/icon', 320, 240),
            $contentItems[0]->getIcon(),
        );
        $this->assertEquals(
            new Image('http://example.com/thumb', 32, 24),
            $contentItems[0]->getThumbnail(),
        );
        $this->assertEquals(
            new ContentItemPlacement(640, 480, PresentationDocumentTarget::Iframe, '_top'),
            $contentItems[0]->getPlacementAdvice(),
        );

        $this->assertArrayHasKey(1, $contentItems);
        $this->assertInstanceOf(FileItem::class, $contentItems[1]);

        $this->assertArrayHasKey(2, $contentItems);
        $this->assertSame(ContentItem::class, $contentItems[2]::class);
        $this->assertSame('Some content', $contentItems[2]->getTitle());

        $score = $contentItems[0]->getLineItem()->getScoreConstraints();
        $this->assertSame(39.5, $score->getNormalMaximum());
        $this->assertSame(2.5, $score->getExtraCreditMaximum());
        $this->assertSame(42.0, $score->getTotalMaximum());
    }

    public function testMapsDataWithAdditionalJsonldContexts(): void
    {
        $data = $this->mapper->map([
            '@context' => [
                'https://www.example.com/some-context',
                'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
                [
                    'foo' => 'https://example.net/some-other-context',
                ],
            ],
            '@graph' => [
                [
                    '@type' => 'FileItem',
                    'mediaType' => 'application/octet-stream',
                    'url' => 'https://example.com/some-file',
                ]
            ],
        ]);

        $this->assertCount(1, $data);
        $this->assertSame('https://example.com/some-file', $data[0]->getUrl());
    }

    public function testFailsToMapIfMissingJsonldContext(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Invalid or unsupported JSON-LD context');

        $this->mapper->map([
            '@context' => 'http://www.example.com/my-context',
            '@graph' => [
                [
                    '@type' => 'FileItem',
                    'mediaType' => 'application/octet-stream',
                    'url' => 'https://example.com/some-file',
                ]
            ],
        ]);
    }
}
