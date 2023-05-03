<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Context;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Exception;
use ML\JsonLD\DocumentLoaderInterface;
use ML\JsonLD\RemoteDocument;
use function file_get_contents;
use const JSON_THROW_ON_ERROR;

final class JsonldDocumentLoader implements DocumentLoaderInterface
{
    public function loadDocument($url): RemoteDocument
    {
        if ($url !== ContentItems::CONTEXT) {
            throw new Exception('Cannot load remote documents');
        }

        return new RemoteDocument(
            'http://purl.imsglobal.org/ctx/lti/v1/ContentItem',
            json_decode(
                file_get_contents(__DIR__ . '/ContentItem.jsonld'),
                flags: JSON_THROW_ON_ERROR,
            ),
        );
    }
}
