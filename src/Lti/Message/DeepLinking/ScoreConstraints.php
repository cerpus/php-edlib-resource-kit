<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Message\DeepLinking;

final readonly class ScoreConstraints
{
    public function __construct(
        private float|null $normalMaximum = null,
        private float|null $extraCreditMaximum = null,
    ) {
    }

    public function getNormalMaximum(): float|null
    {
        return $this->normalMaximum;
    }

    public function getExtraCreditMaximum(): float|null
    {
        return $this->extraCreditMaximum;
    }

    public function getTotalMaximum(): float
    {
        return ($this->normalMaximum ?? 0) + ($this->extraCreditMaximum ?? 0);
    }
}
