<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Context\JsonldDocumentLoader;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use ML\JsonLD\JsonLD;
use stdClass;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final readonly class ContentItemsSerializer implements ContentItemsSerializerInterface
{
    public function __construct(
        private ContentItemSerializerInterface $contentItemSerializer = new ContentItemSerializer(),
        private FileItemSerializerInterface $fileItemSerializer = new FileItemSerializer(),
        private LtiLinkItemSerializerInterface $ltiLinkItemSerializer = new LtiLinkItemSerializer(),
    ) {
    }

    public function serialize(ContentItems $items): array
    {
        $items = iterator_to_array($items);

        $document = array_map(function (ContentItem $item) {
            if ($item instanceof FileItem) {
                return $this->fileItemSerializer->serialize($item);
            }

            if ($item instanceof LtiLinkItem) {
                return $this->ltiLinkItemSerializer->serialize($item);
            }

            return $this->contentItemSerializer->serialize($item);
        }, $items);

        $compacted = JsonLD::compact(
            $this->arraysToObjects($document),
            context: (object) [
                '@context' => ContentItems::CONTEXT,
            ],
            options: [
                'documentLoader' => new JsonldDocumentLoader(),
            ],
        );

        // Ensure the output always has a @graph element.
        // There might be a way of doing this through the compaction algorithm,
        // but this works, too.
        if (!isset($compacted->{'@graph'})) {
            $compacted = (object) [
                '@context' => $compacted->{'@context'},
                '@graph' => [$compacted],
            ];

            unset($compacted->{'@graph'}[0]->{'@context'});
        }

        return $this->objectsToArrays($compacted);
    }

    private function arraysToObjects(array $array): array|stdClass
    {
        return json_decode(
            json_encode($array, flags: JSON_THROW_ON_ERROR),
            associative: false,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    private function objectsToArrays(stdClass $object): array|stdClass
    {
        return json_decode(
            json_encode($object, flags: JSON_THROW_ON_ERROR),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
