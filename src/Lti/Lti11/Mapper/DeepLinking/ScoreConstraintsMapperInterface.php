<?php

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;

interface ScoreConstraintsMapperInterface
{
    public function map(array $data): ScoreConstraints|null;
}
