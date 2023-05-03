<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;

use function array_is_list;
use function count;

/**
 * @template-implements ArrayAccess<int, LtiLinkItem>
 * @template-implements IteratorAggregate<int, LtiLinkItem>
 */
class ContentItems implements ArrayAccess, Countable, IteratorAggregate
{
    public final const CONTEXT = 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem';

    public final const TYPE_CONTENT_ITEM = 'http://purl.imsglobal.org/vocab/lti/v1/ci#ContentItem';
    public final const TYPE_FILE_ITEM = 'http://purl.imsglobal.org/vocab/lti/v1/ci#FileItem';
    public final const TYPE_LTI_LINK_ITEM = 'http://purl.imsglobal.org/vocab/lti/v1/ci#LtiLinkItem';

    public final const PROP_COPY_ADVICE = 'http://purl.imsglobal.org/vocab/lti/v1/ci#copyAdvice';
    public final const PROP_CUSTOM = 'http://purl.imsglobal.org/vocab/lti/v1/ci#custom';
    public final const PROP_EXPIRES_AT = 'http://purl.imsglobal.org/vocab/lti/v1/ci#expiresAt';
    public final const PROP_DISPLAY_HEIGHT = 'http://purl.imsglobal.org/vocab/lti/v1/ci#displayHeight';
    public final const PROP_DISPLAY_WIDTH = 'http://purl.imsglobal.org/vocab/lti/v1/ci#displayWidth';
    public final const PROP_HEIGHT = 'http://purl.imsglobal.org/vocab/lti/v1/ci#height';
    public final const PROP_ICON = 'http://purl.imsglobal.org/vocab/lti/v1/ci#icon';
    public final const PROP_MEDIA_TYPE = 'http://purl.imsglobal.org/vocab/lti/v1/ci#mediaType';
    public final const PROP_PLACEMENT_ADVICE = 'http://purl.imsglobal.org/vocab/lti/v1/ci#placementAdvice';
    public final const PROP_PRESENTATION_DOCUMENT_TARGET = 'http://purl.imsglobal.org/vocab/lti/v1/ci#presentationDocumentTarget';
    public final const PROP_TEXT = 'http://purl.imsglobal.org/vocab/lti/v1/ci#text';
    public final const PROP_THUMBNAIL = 'http://purl.imsglobal.org/vocab/lti/v1/ci#thumbnail';
    public final const PROP_TITLE = 'http://purl.imsglobal.org/vocab/lti/v1/ci#title';
    public final const PROP_URL = 'http://purl.imsglobal.org/vocab/lti/v1/ci#url';
    public final const PROP_WIDTH = 'http://purl.imsglobal.org/vocab/lti/v1/ci#width';
    public final const PROP_WINDOW_TARGET = 'http://purl.imsglobal.org/vocab/lti/v1/ci#windowTarget';

    /**
     * @param list<int, ContentItem> $items
     */
    public function __construct(private readonly array $items)
    {
        assert(array_is_list($items));
        assert(array_filter($items, fn($item) => $item instanceof ContentItem) === $items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): ContentItem
    {
        return $this->items[$offset] ?? throw new OutOfBoundsException();
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('This collection is readonly');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('This collection is readonly');
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
