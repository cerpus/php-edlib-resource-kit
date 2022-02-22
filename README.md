# Edlib Resource Kit

Create custom content types for [Edlib](https://edlib.com/).

## Requirements

* PHP 8.0 or 8.1
* A [PSR-17](https://www.php-fig.org/psr/psr-17/) implementation, e.g.
  [guzzlehttp/psr7](https://packagist.org/packages/guzzlehttp/psr7)
* A [PSR-18](https://www.php-fig.org/psr/psr-18/) compatible HTTP client, e.g.
  [Guzzle](https://packagist.org/packages/guzzlehttp/guzzle)
* A RabbitMQ instance shared with Edlib
* Network access to Edlib internal services

## Installation

~~~sh
composer require cerpus/edlib-resource-kit guzzlehttp/guzzle guzzlehttp/psr7
~~~

## Usage

### Framework integration

We provide [integration with
Laravel](https://github.com/cerpus/php-edlib-resource-kit-laravel) that
simplifies use of this package.

### Configuration

The only required configuration is a RabbitMQ connection that is shared with
Edlib.

~~~php
use Cerpus\EdlibResourceKit\ResourceKit;
use Cerpus\PubSub\Connection\ConnectionFactory;

$connectionFactory = new ConnectionFactory('localhost', 5672, 'guest', 'guest');
$edlib = new ResourceKit($connectionFactory);

// Access the various components
$resourceManager = $edlib->getResourceManager();
$versionManager = $edlib->getResourceVersionManager();
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
use Cerpus\EdlibResourceKit\Resource\ResourceManagerInterface;

class ArticleObserver
{
    public function __construct(private ResourceManagerInterface $manager)
    {
    }

    public function onCreate(Article $article): void
    {
        return $this->manager->save($article->toEdlibResource());
    }

    public function onUpdate(Article $article): void
    {
        return $this->manager->save($article->toEdlibResource());
    }
}
~~~

If everything went well, the resource should now be accessible from within
Edlib.

## Advanced usage

### Overriding the HTTP client & factories

This library will look for and make use of any PSR-18 compatible clients that
are installed (via [HTTPlug Discovery](https://github.com/php-http/discovery)).
You can override the HTTP client with any PSR-18-compatible client of your
choosing:

~~~php
use App\Http\MyClient;
use App\Http\MyRequestFactory;
use Cerpus\EdlibResourceKit\ResourceKit;

$resourceKit = new ResourceKit($pubSub, new MyClient(), new MyRequestFactory());
~~~

You may get better logging and debugging capabilities by wiring up the HTTP
client provided by your framework.

### Adding extra data when publishing to the message bus

It might be necessary to add extra data to a message before publishing it to the
bus. This can be done using a custom serializer:

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
