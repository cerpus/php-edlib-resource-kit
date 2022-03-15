<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Resource\Exception;

use Cerpus\EdlibResourceKit\Exception\ExceptionInterface;
use Exception;

class ResourceSaveFailedException extends Exception implements ExceptionInterface
{
    protected $message = 'Could not save resource';
}
