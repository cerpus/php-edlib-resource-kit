# Edlib Resource Kit

[![codecov](https://codecov.io/github/cerpus/php-edlib-resource-kit/branch/master/graph/badge.svg?token=ZDOKCE9NPA)](https://codecov.io/github/cerpus/php-edlib-resource-kit)

Create custom content types for [Edlib](https://edlib.com/).

## Requirements

* PHP 8.2, 8.3 or 8.4

## Installation

~~~sh
composer require cerpus/edlib-resource-kit
~~~

## Usage

Edlib is notified of new content via the [LTI Content-Item Message standard](http://www.imsglobal.org/specs/lticiv1p0/specification).
Edlib Resource Kit provides message objects, mappers, and serialisers for
working with Content-Item messages.

### Mapping

Map serialised Content-Item graphs to message objects:

```php
use Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking\ContentItemsMapper;

$mapper = new ContentItemsMapper();
$items = $mapper->map(<<<EOJSON
{
    "@context": "http://purl.imsglobal.org/ctx/lti/v1/ContentItem",
    "@graph": [
        {
            "@type": "LtiLinkItem",
            "mediaType": "application/vnd.ims.lti.v1.ltilink",
            "title": "My Cool LTI Content",
            "url": "https://example.com/my-lti-content"
        }
    ]
}
EOJSON);

echo count($items), "\n"; // 1
echo $items[0]->getTitle(), "\n"; // My Cool LTI Content
```

The JSON input must match the compacted JSON-LD representation, as can be seen
in the [LTI Deep Linking 1.0 specification](http://www.imsglobal.org/specs/lticiv1p0/specification).
If the input does not match, a JSON-LD processor can be used to make the input
compliant.

### Serialisation

Convert Content-Item message objects to their serialised JSON representations:

```php
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking\ContentItemsSerializer;

$items = [
    new LtiLinkItem(
        mediaType: 'application/vnd.ims.lti.v1.ltilink',
        title: 'My Cool LTI Content',
        url: 'https://example.com/my-lti-content',
    ),
];

$serializer = new ContentItemsSerializer();
$serialized = $serializer->serialize($items);

echo json_encode($serialized);
```

Output:

```json
{
    "@context": "http://purl.imsglobal.org/ctx/lti/v1/ContentItem",
    "@graph": [
        {
            "@type": "LtiLinkItem",
            "mediaType": "application/vnd.ims.lti.v1.ltilink",
            "title": "My Cool LTI Content",
            "url": "https://example.com/my-lti-content"
        }
    ]
}
```

### Framework integration

We provide [integration with
Laravel](https://github.com/cerpus/php-edlib-resource-kit-laravel) that
simplifies use of this package.

## License

This package is released under the GNU General Public License 3.0. See the
`LICENSE` file for more information.
