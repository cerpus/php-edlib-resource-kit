<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LineItem;

interface LineItemMapperInterface
{
    public function map(array $data): LineItem|null;
}
