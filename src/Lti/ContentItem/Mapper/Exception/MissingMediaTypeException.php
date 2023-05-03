<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\ContentItem\Mapper\Exception;

use Cerpus\EdlibResourceKit\Exception\ExceptionInterface;
use Exception;

class MissingMediaTypeException extends Exception implements ExceptionInterface
{
    protected $message = 'Missing media type';
}
