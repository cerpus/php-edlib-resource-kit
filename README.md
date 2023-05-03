# Edlib Resource Kit

Create custom content types for [Edlib](https://edlib.com/).

## Requirements

* PHP 8.2
* A [PSR-17](https://www.php-fig.org/psr/psr-17/) implementation, e.g.
  [guzzlehttp/psr7](https://packagist.org/packages/guzzlehttp/psr7)
* A [PSR-18](https://www.php-fig.org/psr/psr-18/) compatible HTTP client, e.g.
  [Guzzle 7](https://packagist.org/packages/guzzlehttp/guzzle)
* Network access to Edlib internal services
* A RabbitMQ instance shared with Edlib (optional)

## Installation

~~~sh
composer require cerpus/edlib-resource-kit guzzlehttp/guzzle:^7 guzzlehttp/psr7
~~~

## Usage (Edlib 3)

Rather than communicating via bespoke APIs over private network connections,
Edlib 3 is notified of new content via the [LTI Content-Item Message standard](http://www.imsglobal.org/specs/lticiv1p0/specification).
Edlib Resource Kit provides message objects, mappers, and serialisers for
working with Content-Item messages.

### Mapping

Map serialised Content-Item graphs to message objects:

```php
use Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\ContentItemsMapper;

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

### Serialisation

Convert Content-Item message objects to their serialised [JSON-LD](https://www.w3.org/TR/json-ld11/#introduction)
representations:

```php
use Cerpus\EdlibResourceKit\Lti\ContentItem\ContentItems;
use Cerpus\EdlibResourceKit\Lti\ContentItem\LtiLinkItem;
use Cerpus\EdlibResourceKit\Lti\ContentItem\Serializer\ContentItemsSerializer;

$items = new ContentItems([
    new LtiLinkItem(
        mediaType: 'application/vnd.ims.lti.v1.ltilink',
        title: 'My Cool LTI Content',
        url: 'https://example.com/my-lti-content',
    ),
]);

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
            "http://purl.imsglobal.org/vocab/lti/v1/ci#mediaType": "application/vnd.ims.lti.v1.ltilink",
            "http://purl.imsglobal.org/vocab/lti/v1/ci#title": "My Cool LTI Content",
            "http://purl.imsglobal.org/vocab/lti/v1/ci#url": "https://example.com/my-lti-content"
        }
    ]
}
```

**Note:** The output format might differ from what you expect. In particular,
you may find that keys are fully qualified [IRIs](https://www.w3.org/TR/json-ld11/)
instead of their short names. To handle this, the receiver should normalise the
serialised data according to JSON-LD rules. No guarantees are made as to how the
JSON-LD data is formatted.

## Usage (old Edlib)

### Framework integration

We provide [integration with
Laravel](https://github.com/cerpus/php-edlib-resource-kit-laravel) that
simplifies use of this package.

### Configuration

There are two ways of making Edlib aware of new resources:

* Using the message bus
* Synchronous HTTP request

The HTTP approach is slower, but waits for the publishing of a resource to be
completed, allowing for error handling.

When using the message bus approach, a RabbitMQ connection must be provided:

~~~php
use Cerpus\EdlibResourceKit\ResourceKit;
use Cerpus\PubSub\Connection\ConnectionFactory;

$connectionFactory = new ConnectionFactory('localhost', 5672, 'guest', 'guest');
$resourceKit = new ResourceKit($connectionFactory);
~~~

For the HTTP approach, other than providing the `synchronousResourceManager`
flag, there is no mandatory configuration:

~~~php
use Cerpus\EdlibResourceKit\ResourceKit;

$resourceKit = new ResourceKit(synchronousResourceManager: true);
~~~~

Once you have a ResourceKit instance, you can begin to access the various
components of it:

~~~php
$resourceManager = $resourceKit->getResourceManager();
$versionManager = $resourecKit->getResourceVersionManager();
~~~~

### Notifying Edlib of content updates

Given a model class representing an item of your custom content type (in this
case an article):

~~~php
namespace App\Models;

use Cerpus\EdlibResourceKit\Contract\EdlibResource;

class Article
{
    // Database-mapped article ID
    private string $id;

    public function toEdlibResource(): EdlibResource
    {
        return new ArticleEdlibResource($this->id, /* ... */);
    }
}
~~~

Create an accompanying `EdlibResource` class:

~~~php
namespace App\DataObjects;

use Cerpus\EdlibResourceKit\Contract\EdlibResource;

class ArticleEdlibResource implements EdlibResource
{
    public function __construct(private string $systemId, /* ... */)
    {
    }

    public function getSystemName(): string
    {
        return 'my-unique-and-persistent-system-name';
    }

    public function getSystemId(): string
    {
        return $this->systemId;
    }

    public function getContentType(): string|null
    {
        return 'article';
    }

    // ... implement the remaining EdlibResource methods here
}
~~~

When the article is created or updated, a corresponding call to the resource
manager must take place. The procedure will vary depending on your framework of
choice, but here it is demonstrated using the observer pattern:

~~~php
use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\Resource\ResourceManagerInterface;

class ArticleObserver
{
    public function __construct(private ResourceManagerInterface $manager)
    {
    }

    public function onCreate(Article $article): void
    {
        $this->save($article->toEdlibResource());
    }

    public function onUpdate(Article $article): void
    {
        $this->save($article->toEdlibResource());
    }

    private function save(EdlibResource $resource): void
    {
        try {
            return $this->manager->save($resource);
        } catch (ResourceSaveFailedException $e) {
            // handle the failure somehow
        }
    }
}
~~~

If everything went well, the resource should now be accessible from within
Edlib.

## Advanced usage

### Overriding the HTTP client & message factories

This library will look for and make use of any PSR-17 compatible message
factories and PSR-18 compatible HTTP clients that are installed (via
[HTTPlug Discovery](https://github.com/php-http/discovery)). You can override
these with factories & clients of your choosing:

~~~php
use App\Http\MyClient;
use App\Http\MyRequestFactory;
use App\Http\MyStreamFactory;
use Cerpus\EdlibResourceKit\ResourceKit;

$resourceKit = new ResourceKit(
    httpClient: new MyClient(),
    requestFactory: new MyRequestFactory(),
    streamFactory: new MyStreamFactory(),
    synchronousResourceManager: true,
);
~~~

By wiring up the HTTP client provided by your web framework, you may get better
logging & debugging capabilities.

### Adding extra data when publishing resources

It might be necessary to add extra data to a resource before publishing it. This
can be done using a custom serializer:

~~~php
use Cerpus\EdlibResourceKit\Contract\EdlibResource;
use Cerpus\EdlibResourceKit\ResourceKit;
use Cerpus\EdlibResourceKit\Serializer\ResourceSerializer;

class MySerializer extends ResourceSerializer
{
    public function serialize(EdlibResource $resource): array
    {
        $data = parent::serialize($resource);

        if ($resource instanceof MyEdlibResource) {
            $data['some_custom_key'] => $resource->getMyCustomData();
        }

        return $data;
    }
}

$resourceKit = new ResourceKit($pubSub, resourceSerializer: new MySerializer());
~~~

## License

This package is released under the GNU General Public License 3.0. See the
`LICENSE` file for more information.

## Attribution

* [context.jsonld](src/Lti/ContentItem/Context/ContentItem.jsonld)

  Retrieved from <https://www.imsglobal.org/lti/model/mediatype/application/vnd/ims/lti/v1/contentitems+json/context.json>
  on 28th April 2023.
