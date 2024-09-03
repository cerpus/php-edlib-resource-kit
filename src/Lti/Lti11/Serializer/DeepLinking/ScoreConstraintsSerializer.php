<?php

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;

final readonly class ScoreConstraintsSerializer implements ScoreConstraintsSerializerInterface
{
    public function serialize(ScoreConstraints $scoreConstraints): array
    {
        return [
            '@type' => 'NumericLimits',
            Prop::NORMAL_MAXIMUM => $scoreConstraints->getNormalMaximum() ?? 0,
            Prop::EXTRA_CREDIT_MAXIMUM => $scoreConstraints->getExtraCreditMaximum() ?? 0,
            Prop::TOTAL_MAXIMUM => $scoreConstraints->getTotalMaximum(),
        ];
    }
}
