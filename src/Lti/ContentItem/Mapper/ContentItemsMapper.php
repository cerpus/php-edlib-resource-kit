<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper;

use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItemPlacement;
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Context\JsonldDocumentLoader;
use Cerpus\EdlibResourceKit\Lti\ContentItem\FileItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Image;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\Exception\MissingMediaTypeException;
use Cerpus\EdlibResourceKit\Lti\ContentItem\PresentationDocumentTarget;
use DateTimeImmutable;
use ML\JsonLD\DocumentLoaderInterface;
use ML\JsonLD\JsonLD;
use stdClass;
use function array_is_list;
use function assert;
use function is_array;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final readonly class ContentItemsMapper implements ContentItemsMapperInterface
{
    public function __construct(
        private DocumentLoaderInterface $documentLoader = new JsonldDocumentLoader(),
    ) {
    }

    public function map(string|array|stdClass $dataOrJson): ContentItems
    {
        if (is_string($dataOrJson)) {
            $data = json_decode($dataOrJson, flags: JSON_THROW_ON_ERROR);
            assert($data instanceof stdClass);
        } elseif (is_array($dataOrJson)) {
            $data = $this->arrayToStdclass($dataOrJson);
        } else {
            $data = $dataOrJson;
        }

        $items = JsonLD::expand($data, options: [
            'documentLoader' => $this->documentLoader,
        ]);

        return new ContentItems(array_map(function (stdClass $item) {
            return match ($item->{'@type'}[0] ?? '') {
                ContentItems::TYPE_FILE_ITEM => $this->mapFileItem($item),
                ContentItems::TYPE_LTI_LINK_ITEM => $this->mapLtiLinkItem($item),
                default => $this->mapContentItem($item),
            };
        }, $items));
    }

    private function mapContentItem(stdClass $item): ContentItem
    {
        return new ContentItem(
            $item->{ContentItems::PROP_MEDIA_TYPE}[0]->{'@value'}
                ?? throw new MissingMediaTypeException(),
            $this->mapImage($item, ContentItems::PROP_ICON),
            $this->mapPlacementAdvice($item),
            $item->{ContentItems::PROP_TEXT}[0]->{'@value'} ?? null,
            $this->mapImage($item, ContentItems::PROP_THUMBNAIL),
            $item->{ContentItems::PROP_TITLE}[0]->{'@value'} ?? null,
            $item->{ContentItems::PROP_URL}[0]->{'@value'} ?? null,
        );
    }

    private function mapFileItem(stdClass $item): FileItem
    {
        return new FileItem(
            $item->{ContentItems::PROP_MEDIA_TYPE}[0]->{'@value'}
                ?? throw new MissingMediaTypeException(),
            $item->{ContentItems::PROP_COPY_ADVICE}[0]->{'@value'} ?? null,
            isset($item->{ContentItems::PROP_EXPIRES_AT}[0]->{'@value'})
                ? new DateTimeImmutable($item->{ContentItems::PROP_EXPIRES_AT}[0]->{'@value'})
                : null,
            $this->mapImage($item, ContentItems::PROP_ICON),
            $this->mapPlacementAdvice($item),
            $item->{ContentItems::PROP_TEXT}[0]->{'@value'} ?? null,
            $this->mapImage($item, ContentItems::PROP_THUMBNAIL),
            $item->{ContentItems::PROP_TITLE}[0]->{'@value'} ?? null,
            $item->{ContentItems::PROP_URL}[0]->{'@value'} ?? null,
        );
    }

    private function mapLtiLinkItem(stdClass $item): LtiLinkItem
    {
        return new LtiLinkItem(
            $item->{ContentItems::PROP_MEDIA_TYPE}[0]->{'@value'}
                ?? throw new MissingMediaTypeException(),
            $this->mapImage($item, ContentItems::PROP_ICON),
            $this->mapPlacementAdvice($item),
            $this->mapImage($item, ContentItems::PROP_THUMBNAIL),
            $item->{ContentItems::PROP_TEXT}[0]->{'@value'} ?? null,
            $item->{ContentItems::PROP_TITLE}[0]->{'@value'} ?? null,
            $item->{ContentItems::PROP_URL}[0]->{'@value'} ?? null,
        );
    }

    private function mapPlacementAdvice(stdClass $data): ContentItemPlacement|null
    {
        $advice = $data->{ContentItems::PROP_PLACEMENT_ADVICE}[0] ?? null;

        if ($advice === null) {
            return null;
        }

        return new ContentItemPlacement(
            $advice->{ContentItems::PROP_DISPLAY_WIDTH}[0]->{'@value'} ?? null,
            $advice->{ContentItems::PROP_DISPLAY_HEIGHT}[0]->{'@value'} ?? null,
            // FIXME: not sure why sometimes this is @value and other times @id?
            PresentationDocumentTarget::tryFromShortName(
                $advice->{ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET}[0]->{'@value'} ??
                $advice->{ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET}[0]->{'@id'}
            ) ?? PresentationDocumentTarget::from(
                $advice->{ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET}[0]->{'@value'} ??
                $advice->{ContentItems::PROP_PRESENTATION_DOCUMENT_TARGET}[0]->{'@id'}
            ),
            $advice->{ContentItems::PROP_WINDOW_TARGET}[0]->{'@value'} ?? null,
        );
    }

    private function mapImage(stdClass $data, string $path): Image|null
    {
        $image = $data->{$path}[0] ?? null;

        if (!$image) {
            return null;
        }

        return new Image(
            $image->{'@id'},
            $image->{ContentItems::PROP_WIDTH}[0]->{'@value'} ?? null,
            $image->{ContentItems::PROP_HEIGHT}[0]->{'@value'} ?? null,
        );
    }

    private static function arrayToStdclass(array $associativeArray): stdClass
    {
        return (object) array_map(
            fn($value) => is_array($value) && !array_is_list($value)
                ? $this->arrayToStdclass($value)
                : $value,
            $associativeArray,
        );
    }
}
