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
    public final const VOCAB = 'http://purl.imsglobal.org/vocab/lti/v1/ci#';

    public final const TYPE_CONTENT_ITEM = self::VOCAB . 'ContentItem';
    public final const TYPE_FILE_ITEM = self::VOCAB . 'FileItem';
    public final const TYPE_LTI_LINK_ITEM = self::VOCAB . 'LtiLinkItem';

    public final const PROP_COPY_ADVICE = self::VOCAB . 'copyAdvice';
    public final const PROP_EXPIRES_AT = self::VOCAB . 'expiresAt';
    public final const PROP_DISPLAY_HEIGHT = self::VOCAB . 'displayHeight';
    public final const PROP_DISPLAY_WIDTH = self::VOCAB . 'displayWidth';
    public final const PROP_HEIGHT = self::VOCAB . 'height';
    public final const PROP_ICON = self::VOCAB . 'icon';
    public final const PROP_MEDIA_TYPE = self::VOCAB . 'mediaType';
    public final const PROP_PLACEMENT_ADVICE = self::VOCAB . 'placementAdvice';
    public final const PROP_PRESENTATION_DOCUMENT_TARGET = self::VOCAB . 'presentationDocumentTarget';
    public final const PROP_TEXT = self::VOCAB . 'text';
    public final const PROP_THUMBNAIL = self::VOCAB . 'thumbnail';
    public final const PROP_TITLE = self::VOCAB . 'title';
    public final const PROP_URL = self::VOCAB . 'url';
    public final const PROP_WIDTH = self::VOCAB . 'width';
    public final const PROP_WINDOW_TARGET = self::VOCAB . 'windowTarget';

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
