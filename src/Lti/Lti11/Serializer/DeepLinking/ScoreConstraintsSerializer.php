<?php

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\ScoreConstraints;

final readonly class ScoreConstraintsSerializer implements ScoreConstraintsSerializerInterface
{
    public function serialize(ScoreConstraints $scoreConstraints): array
    {
        $serialized = [
            '@type' => 'NumericLimits',
        ];

        if ($scoreConstraints->getNormalMaximum() !== null) {
            $serialized[Prop::NORMAL_MAXIMUM] = $scoreConstraints->getNormalMaximum();
        }

        if ($scoreConstraints->getExtraCreditMaximum() !== null) {
            $serialized[Prop::EXTRA_CREDIT_MAXIMUM] = $scoreConstraints->getExtraCreditMaximum();
        }

        if ($scoreConstraints->getTotalMaximum() !== null) {
            $serialized[Prop::TOTAL_MAXIMUM] = $scoreConstraints->getTotalMaximum();
        }

        return $serialized;
    }
}
