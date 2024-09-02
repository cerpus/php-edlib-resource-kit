<?php

namespace Cerpus\EdlibResourceKit\Lti\Message\DeepLinking;

final readonly class LineItem
{
    public function __construct(
        private ScoreConstraints|null $scoreConstraints = null,
    ) {
    }

    public function getScoreConstraints(): ScoreConstraints|null
    {
        return $this->scoreConstraints;
    }
}
