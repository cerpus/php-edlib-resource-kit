<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;

interface ScoreConstraintsSerializerInterface
{
    public function serialize(ScoreConstraints $scoreConstraints): array;
}
