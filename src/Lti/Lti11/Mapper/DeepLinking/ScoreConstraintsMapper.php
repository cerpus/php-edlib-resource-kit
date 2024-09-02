<?php

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;

final readonly class ScoreConstraintsMapper implements ScoreConstraintsMapperInterface
{
    public function map(array $data): ScoreConstraints|null
    {
        return new ScoreConstraints(
            Prop::getNormalMaximum($data),
            Prop::getExtraCreditMaximum($data),
        );
    }
}
