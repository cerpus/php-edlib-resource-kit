<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Tests\Lti\Edlib;

use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibContentItemsSerializer;
use Cerpus\EdlibResourceKit\Lti\Edlib\DeepLinking\EdlibLtiLinkItem;
use PHPUnit\Framework\TestCase;

final class EdlibContentItemsSerializerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->serializer = new EdlibContentItemsSerializer();
    }

    public function testAddsJsonldContextAndSerializesEdlibExtensions(): void
    {
        $data = $this->serializer->serialize([
            (new EdlibLtiLinkItem(title: 'Foo'))
                ->withLanguageIso639_3('eng')
                ->withLicense('MIT')
                ->withPublished(false)
                ->withShared(true)
                ->withTags(['foo'])
        ]);

        $this->assertArrayHasKey('@context', $data);
        $this->assertIsArray($data['@context']);

        $this->assertArrayHasKey(0, $data['@context']);
        $this->assertSame('http://purl.imsglobal.org/ctx/lti/v1/ContentItem', $data['@context'][0]);

        $this->assertArrayHasKey(1, $data['@context']);
        $this->assertIsArray($data['@context'][1]);
        $this->assertArrayHasKey('edlib', $data['@context'][1]);

        $this->assertArrayHasKey('@graph', $data);
        $this->assertIsArray($data['@graph']);
        $this->assertArrayHasKey(0, $data['@graph']);
        $this->assertIsArray($data['@graph'][0]);

        $this->assertArrayHasKey('languageIso639_3', $data['@graph'][0]);
        $this->assertSame('eng', $data['@graph'][0]['languageIso639_3']);

        $this->assertArrayHasKey('license', $data['@graph'][0]);
        $this->assertSame('MIT', $data['@graph'][0]['license']);

        $this->assertArrayHasKey('published', $data['@graph'][0]);
        $this->assertFalse($data['@graph'][0]['published']);

        $this->assertArrayHasKey('shared', $data['@graph'][0]);
        $this->assertTrue($data['@graph'][0]['shared']);

        $this->assertArrayHasKey('tag', $data['@graph'][0]);
        $this->assertSame('foo', $data['@graph'][0]['tag']);
    }

    public function testSerializesMultipleTags(): void
    {
        $data = $this->serializer->serialize([
            (new EdlibLtiLinkItem)->withTags(['foo', 'bar'])
        ]);

        $this->assertArrayHasKey('tag', $data['@graph'][0]);
        $this->assertSame(['foo', 'bar'], $data['@graph'][0]['tag']);
    }
}
