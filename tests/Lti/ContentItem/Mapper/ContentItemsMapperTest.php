<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\ContentItem\Mapper;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\ContentItemsMapper;
use Cerpus\EdlibResourceKit\Lti\ContentItem\PresentationDocumentTarget;
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
        $contentItems = $this->mapper->map(json_encode([
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
        ]));

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
    }
}
