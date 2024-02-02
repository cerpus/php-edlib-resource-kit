<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\Edlib;

use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibContentItemMapper;
use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibLtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking\ContentItemsMapper;
use PHPUnit\Framework\TestCase;

final class EdlibContentItemMapperTest extends TestCase
{
    public function testMapsEdlibExtensions(): void
    {
        $mapper = new ContentItemsMapper(new EdlibContentItemMapper());

        $items = $mapper->map([
            '@context' => 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            '@graph' => [
                [
                    '@type' => 'LtiLinkItem',
                    'mediaType' => 'application/vnd.ims.lti.v1.ltilink',
                    'title' => 'My Cool LTI Content',
                    'url' => 'https://example.com/my-lti-content',
                    'languageIso639_3' => 'eng',
                    'license' => 'MIT',
                ],
            ],
        ]);

        $this->assertCount(1, $items);
        $this->assertArrayHasKey(0, $items);
        $this->assertInstanceOf(EdlibLtiLinkItem::class, $items[0]);
        $this->assertSame('My Cool LTI Content', $items[0]->getTitle());
        $this->assertSame('eng', $items[0]->getLanguageIso639_3());
        $this->assertSame('MIT', $items[0]->getLicense());
    }
}
