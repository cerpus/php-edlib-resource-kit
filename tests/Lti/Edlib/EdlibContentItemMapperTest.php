<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\Edlib;

use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibContentItemMapper;
use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibLtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking\ContentItemsMapper;
use PHPUnit\Framework\TestCase;

final class EdlibContentItemMapperTest extends TestCase
{
    private ContentItemsMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ContentItemsMapper(new EdlibContentItemMapper());
    }

    public function testMapsEdlibExtensions(): void
    {
        $items = $this->mapper->map([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'title' => 'My Cool LTI Content',
                    'url' => 'https://example.com/my-lti-content',
                    'edlibVersionId' => '9876543210',
                    'languageIso639_3' => 'eng',
                    'license' => 'MIT',
                    'published' => true,
                    'shared' => false,
                    'tag' => 'foo',
                    'ownerEmail' => 'owner@example.com',
                ],
            ],
        ]);

        $this->assertCount(1, $items);
        $this->assertArrayHasKey(0, $items);
        $this->assertInstanceOf(EdlibLtiLinkItem::class, $items[0]);
        $this->assertSame('My Cool LTI Content', $items[0]->getTitle());
        $this->assertSame('9876543210', $items[0]->getEdlibVersionId());
        $this->assertSame('eng', $items[0]->getLanguageIso639_3());
        $this->assertSame('MIT', $items[0]->getLicense());
        $this->assertFalse($items[0]->isShared());
        $this->assertTrue($items[0]->isPublished());
        $this->assertSame(['foo'], $items[0]->getTags());
        $this->assertSame('owner@example.com', $items[0]->getOwnerEmail());
    }

    public function testMapsMultipleTags(): void
    {
        $items = $this->mapper->map([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'tag' => ['foo', 'bar'],
                ],
            ],
        ]);

        $this->assertCount(1, $items);
        $this->assertSame(['foo', 'bar'], $items[0]->getTags());
    }
}
